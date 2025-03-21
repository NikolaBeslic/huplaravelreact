<?php

namespace App\Http\Controllers;

use App\Http\Resources\IgranjeResource;
use App\Http\Resources\PozoristeResource;
use App\Igranje;
use Illuminate\Http\Request;
use App\Models\Pozoriste;
use Dotenv\Exception\ValidationException;
use Exception;

class PozoristaController extends Controller
{
    //
    public function getPozorista()
    {
        return PozoristeResource::collection(Pozoriste::orderBy('naziv_pozorista', 'asc')->get());
    }

    public function getAllPozorista()
    {
        $pozorista = Pozoriste::select('pozoristeid', 'naziv_pozorista', 'pozoriste_slug', 'url_logo AS logo')->orderBy('naziv_pozorista')->get();
        return json_encode($pozorista);
    }

    public function getSinglePozoriste($pozoriste_slug)
    {
        $pozoriste = Pozoriste::where('pozoriste_slug', $pozoriste_slug)
            ->with(['igranja.predstava' => function ($query) {
                $query->select('predstavaid', 'naziv_predstave', 'predstava_slug', 'plakat'); // Specify the columns you want to load
                // }])->with(['tekstovi.kategorija'])
            }])->with(['tekstovi' => function ($query) {
                $query->select('tekst.tekstid', 'naslov', 'slug', 'tekst_photo', 'published_at', 'created_at', 'kategorijaid'); // Specify the columns you want to load
            }, 'tekstovi.kategorija'])
            ->with(['predstave' => function ($query) {
                $query->select('predstava.predstavaid', 'naziv_predstave', 'predstava_slug', 'plakat', 'premijera')->orderBy('premijera', 'desc');
            }, 'predstave.zanrovi'])
            ->firstOrFail();

        if (auth('sanctum')->user()) {
            $pozoriste->omiljenoKorisnika = $this->getOmiljenoKorisnika($pozoriste);
        }

        return json_encode($pozoriste);
        // return $pozoriste;
        //return IgranjeResource::collection($pozoriste->igranja);
        //return PozoristeResource::make($pozoriste);

    }

    public function getSinglePozoristeAdmin($pozoristeid)
    {
        $pozoriste = Pozoriste::where('pozoristeid', $pozoristeid)->firstOrFail();
        return json_encode($pozoriste);
    }

    public function update(Request $request)
    {
        $pozoriste = Pozoriste::where('pozoristeid', $request->pozoristeid)->firstOrFail();
        try {
            $request->validate([
                'naziv_pozorista' => 'required',
                'pozoriste_slug' => 'required',
                'gradid' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }
        $pozoriste->fill($request->all());

        if ($pozoriste->save()) {
            return response()->json([], 200);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'naziv_pozorista' => 'required',
                'pozoriste_slug' => 'required|unique:pozoriste,pozoriste_slug',
                'gradid' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }
        $pozoriste = new Pozoriste($request->all());

        if ($pozoriste->save()) {
            return response()->json([], 200);
        }
    }

    public function dodajUOmiljena(Request $request)
    {
        $pozoriste = Pozoriste::find($request->pozoristeId);
        $korisnik = auth('sanctum')->user();
        try {
            $pozoriste->omiljeno()->attach($korisnik->id);
            return response()->json();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getOmiljenoKorisnika($pozoriste)
    {
        $korisnikId = auth('sanctum')->user()->id;
        return $pozoriste->omiljeno()->where('korisnikid', $korisnikId)->exists();
    }

    public function getPozoristeWithPredstave($pozoriste_slug)
    {
        $pozoriste = Pozoriste::where('pozoriste_slug', $pozoriste_slug)
            ->with([
                'predstave' =>  function ($query) {
                    $query->orderBy('naziv_predstave');
                },
                'scene'
            ])

            ->firstOrFail();
        return json_encode($pozoriste);
    }
}
