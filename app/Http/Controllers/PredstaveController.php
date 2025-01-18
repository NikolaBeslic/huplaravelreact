<?php

namespace App\Http\Controllers;

use App\Http\Resources\PredstavaResource;
use App\Models\Grad;
use Illuminate\Http\Request;
use App\Models\Predstava;
use App\Models\Zanr;
use App\Models\Ocena;
use Dotenv\Exception\ValidationException;
use Exception;
use stdClass;
use Illuminate\Support\Facades\Auth;

class PredstaveController extends Controller
{
    //
    public function getPredstave()
    {
        return json_encode(
            Predstava::whereNotNull('plakat')
                //->where('pozoristeid', 1)
                ->whereHas('zanrovi')
                ->with('zanrovi')
                ->with('pozorista.grad')
                ->orderBy('created_at', 'desc')
                ->take(1000)
                ->get()
        );
    }

    public function getAllPredstave()
    {
        $allPredstave = Predstava::select('predstavaid', 'naziv_predstave')->orderBy('naziv_predstave')->get();
        return json_encode($allPredstave);
    }

    public function getSinglePredstava($predstava_slug)
    {
        $predstava = Predstava::where('predstava_slug', $predstava_slug)
            ->with('pozorista')
            ->with('tekstovi')
            ->with('zanrovi')
            ->with('ocena')
            ->with('igranja')
            ->with('komentari')
            ->firstOrFail();
        $predstava->prosecnaOcena = round($predstava->ocena()->avg('ocena'), 1);
        $predstava->brojOcena = $predstava->ocena()->count();
        $predstava->ocenaKorisnika = $this->getOcenaKorisnika($predstava);
        $predstava->naListiZeljaKorisnika = $this->getNaListiZelja($predstava);
        return PredstavaResource::make($predstava);
    }
    public function getOcenaKorisnika($predstava)
    {
        $korisnikid = auth('sanctum')->user()->id;
        $ocenaKorisnika = Ocena::where(['predstavaid' => $predstava->predstavaid, 'korisnikid' => $korisnikid])->value('ocena');
        return $ocenaKorisnika;
    }

    public function getNaListiZelja($predstava)
    {
        $korisnikid = auth('sanctum')->user()->id;
        $naListiZelja = $predstava->naListiZelja()->where('korisnikid', $korisnikid)->exists();
        return $naListiZelja;
    }

    public function getPredstaveZaNaslovnu()
    {
        $predstave = new stdClass();
        $predstave->najnovije  =  $this->getNajnovijePredstave();
        $predstave->najpopularnije = $this->getNajpopularnijePredstave();
        return json_encode($predstave);
    }

    public function getNajnovijePredstave()
    {
        return $predstave = Predstava::select('predstavaid', 'naziv_predstave', 'predstava_slug', 'premijera', 'plakat',)
            ->has('pozorista')
            ->whereNotNull('plakat')
            ->with([
                'pozorista' => function ($query) {
                    $query->select('pozoriste.pozoristeid', 'pozoriste_slug', 'naziv_pozorista');
                }
            ])
            ->orderBy('premijera', 'desc')
            ->take(10)
            ->get();
        return json_encode($predstave);
    }

    public function getNajpopularnijePredstave()
    {
        return $predstave =
            Predstava::with([
                'pozorista' => function ($query) {
                    $query->select('pozoriste.pozoristeid', 'pozoriste_slug', 'naziv_pozorista');
                }
            ])
            ->with('ocena')
            ->select('predstava.predstavaid', 'naziv_predstave', 'predstava_slug', 'premijera', 'plakat')
            ->leftJoin('ocenio', 'predstava.predstavaid', '=', 'ocenio.predstavaid')
            ->selectRaw('ROUND(AVG(ocenio.ocena), 1) as prosecna_ocena, COUNT(ocenio.korisnikid) as broj_ocena')
            ->has('ocena')
            ->whereNotNull('plakat')
            ->groupBy('predstava.predstavaid', 'naziv_predstave', 'predstava_slug', 'premijera', 'plakat')
            ->having('broj_ocena', '>', 5)
            ->orderBy('prosecna_ocena', 'desc')
            ->take(10)
            ->get();

        return json_encode($predstave);
    }

    public function getZanrovi()
    {
        return json_encode(Zanr::all());
    }

    public function getGradovi()
    {
        return json_encode(Grad::orderBy('naziv_grada')->get());
    }

    public function getPremijere()
    {
        $premijere = Predstava::where('premijera', '>', date('2024-06-01'))
            ->whereNotNull('plakat')
            ->orderBy('premijera', 'asc')
            ->with('pozorista')
            ->take(5)
            ->get();
        return json_encode($premijere);
    }

    public function getPredstaveWithTekst()
    {
        $predstave = Predstava::whereHas('tekstovi')
            ->whereNotNull('plakat')
            //->orderBy('tekst.created_at', 'desc')
            ->take(10)
            ->get();
        return json_encode($predstave);
    }

    public function getAllPredstaveAdmin()
    {
        return json_encode(Predstava::with('pozorista')->get());
    }

    public function getSinglePredstavaById($predstavaid)
    {
        $predstava = Predstava::where('predstavaid', $predstavaid)->with('pozorista')->firstOrFail();
        return json_encode($predstava);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'naziv_predstave' => 'required',
                'predstava_slug' => 'required|unique:predstava'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $predstava = new Predstava($request->all());
        if ($predstava->save()) {
            $predstava->pozorista()->attach($request->pozorista);
            return response()->json([], 200);
        }
    }

    public function update(Request $request)
    {
        $predstava = Predstava::where('predstavaid', $request->predstavaid)->firstOrFail();

        try {
            $request->validate([
                'naziv_predstave' => 'required',
                'predstava_slug' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $predstava->fill($request->all());
        if ($predstava->save()) {
            $predstava->pozorista()->sync($request->pozorista);
            return response()->json([], 200);
        }
    }

    public function adminGetPredstaveZaNaslovnu()
    {
        $predstave = Predstava::with('pozorista')->orderBy('created_at', 'desc')->take(10)->get();
        return json_encode($predstave);
    }

    public function oceni(Request $request)
    {
        $korisnik = $request->user();
        $predstava = Predstava::find($request->predstavaid);
        $ocena = new Ocena(['korisnikid' => $korisnik->id, 'predstavaid' => $predstava->predstavaid, 'ocena' => $request->ocena]);
        if ($ocena->save()) {
            $predstava->prosecnaOcena = round($predstava->ocena()->avg('ocena'), 1);
            return $this->getSinglePredstava($predstava->predstava_slug);
        }
    }

    public function dodajNaListuZelja(Request $request)
    {
        $predstava = Predstava::find($request->predstavaid);
        $korisnik = $request->user();
        try {
            $predstava->naListiZelja()->attach($korisnik, ['statuszeljeid' => 1]);
            return response()->json();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
