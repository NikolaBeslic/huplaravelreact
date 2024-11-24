<?php

namespace App\Http\Controllers;

use App\Http\Resources\PredstavaResource;
use App\Models\Grad;
use Illuminate\Http\Request;
use App\Models\Predstava;
use App\Models\Zanr;
use Illuminate\Support\Facades\Date;
use PhpParser\Node\Expr\Cast\Object_;
use stdClass;

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
        return PredstavaResource::make($predstava);
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
}
