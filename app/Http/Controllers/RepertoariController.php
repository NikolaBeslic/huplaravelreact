<?php

namespace App\Http\Controllers;

use App\Models\Igranje;
use App\Models\Pozoriste;
use App\Models\Predstava;
use App\Models\Scena;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use stdClass;

class RepertoariController extends Controller
{
    //
    public function __construct()
    {

        $this->middleware('auth:admin')->only(['create', 'store', 'adminindex', 'edit', 'adminshow', 'update', 'delete', 'dodajRepertoar', 'prikaziFormuZaReperotar', 'dodajGostovanje', 'destroy']);
    }

    public function getJsonRepertoari()
    {
        $igranja = Igranje::with(['pozoriste' => function ($query) {
            $query->select('naziv_pozorista', 'pozoriste_slug', 'pozoristeid', 'gradid')
                ->with(['grad' => function ($query) {
                    $query->select('naziv_grada', 'gradid');
                }]);
        }])
            ->with(['predstava' => function ($query) {
                $query->select('naziv_predstave', 'predstava_slug', 'predstavaid', 'plakat')
                    ->with(['zanrovi' => function ($query) {
                        $query->select('naziv_zanra', 'zanr_slug');
                    }]);
            }])->with('scena')->orderBy('datum', 'asc')
            ->take(100)->get();
        foreach ($igranja as $igr) {
            $igr->title = $igr->predstava->naziv_predstave;
            $igr->description = $igr->pozoriste->naziv_pozorista;
            $igr->date = $igr->datum . ' ' . $igr->vreme;
            $igr->url = '/predstave/' . $igr->predstava->predstava_slug;
        }
        return json_encode($igranja);
    }


    public function getDanasNaRepertoaru()
    {
        $igranja = Igranje::query()
            ->select([
                'seigraid',
                'datum',
                'vreme',
                'pozoristeid',
                'predstavaid',
                'scenaid',
            ])
            ->whereDate('datum', '2019-09-20')
            ->orderBy('datum')
            ->orderBy('vreme')
            ->limit(10)
            ->with([
                'pozoriste:pozoristeid,naziv_pozorista,pozoriste_slug,gradid',
                'scena:scenaid,naziv_scene',
                'predstava:predstavaid,naziv_predstave,predstava_slug,plakat',
                'predstava.zanrovi:zanrid,naziv_zanra,zanr_slug',
            ])
            ->get();
        return json_encode($igranja);
    }

    public function igranjeStore(Request $request)
    {

        try {
            $request->validate([
                'pozoristeid' => 'required',
                'predstavaid' => 'required',
                'vreme' => 'required|',
                'datum' => 'required|date',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $igranje = new Igranje($request->all());

        if (!$this->daLiJePozoristeSlobodno($igranje))
            return response()->json('Pozoriste je zauzeto u ovom terminu', 500);

        if (!$this->daLiSePredstavaVecIgraNegde($igranje))
            return response()->json('Predstava se vec igra negde u ovom terminu', 500);


        if ($igranje->save()) {
            if ($request->gostovanje) {
                $igranjaPozorista = $this->fetchSvaIgranjaFromDb();
            } else {
                $igranjaPozorista = $this->fetchIgranjaFromDb($request->pozoristeid);
            }
            return response()->json($igranjaPozorista);
        }
        return response()->json('greska prilikom cuvanja izvodjenja', 500);
    }

    public function igranjeMultiStore(Request $request)
    {
        $igranjaData = $request['igranja'];

        $savedIgranja = [];
        $errors = [];

        foreach ($igranjaData as $data) {
            $igranje = new Igranje($data);

            if (!$this->daLiJePozoristeSlobodno($igranje)) {
                $errors[] = "Pozoriste je zauzeto za predstavu ID: {$data['predstavaid']} na datum {$data['datum']} u {$data['vreme']}";
                continue;
            }

            if (!$this->daLiSePredstavaVecIgraNegde($igranje)) {
                $errors[] = "Predstava ID: {$data['predstavaid']} se vec igra na datum {$data['datum']} u {$data['vreme']}";
                continue;
            }

            if ($igranje->save()) {
                $savedIgranja[] = $igranje;
            } else {
                $errors[] = "Greska prilikom cuvanja izvodjenja za predstavu ID: {$data['predstavaid']}";
            }
        }

        $response = new stdClass();
        $response->saved = $savedIgranja;
        $response->errors = $errors;

        return response()->json($response);
    }

    public function igranjeUpdate(Request $request)
    {

        try {
            $request->validate([
                'seigraid' => 'required',
                'pozoristeid' => 'required',
                'vreme' => 'required|',
                'datum' => 'required|date',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $igranje = Igranje::find($request->seigraid);
        if (!$igranje) {
            return response()->json('Ne postoji izvodjenje sa ovim ID', 500);
        }

        $igranje->fill($request->all());

        /* if (!$this->daLiJePozoristeSlobodno($igranje))
            return response()->json('Pozoriste je zauzeto u ovom terminu', 500);

        if (!$this->daLiSePredstavaVecIgraNegde($igranje))
            return response()->json('Predstava se vec igra negde u ovom terminu', 500); */

        if ($igranje->save()) {
            if ($request->gostovanje) {
                $igranjaPozorista = $this->fetchSvaIgranjaFromDb();
            } else {
                $igranjaPozorista = $this->fetchIgranjaFromDb($request->pozoristeid);
            }
            return response()->json($igranjaPozorista);
        }
    }

    public function igranjeDelete($id)
    {
        $igranje = Igranje::find($id);
        if (!$igranje) {
            return response()->json('Ne postoji izvodjenje sa ovim ID', 500);
        }
        // $pozoristeid = $igranje->pozoristeid;
        if ($igranje->delete()) {
            //$igranjaPozorista = $this->fetchIgranjaFromDb($pozoristeid);
            return response()->json();
        }
        return response()->json('Greska prilikom brisanja izvodjenja', 500);
    }

    public function getIgranjaPozorista($pozoristeid)
    {
        $igranja = $this->fetchIgranjaFromDb($pozoristeid);
        return json_encode($igranja);
    }

    public function fetchIgranjaFromDb($pozoristeid)
    {
        $igranja = Igranje::where('pozoristeid', $pozoristeid)
            ->with(['pozoriste' => function ($query) {
                $query->select('naziv_pozorista', 'pozoriste_slug');
            }])
            ->with(['predstava' => function ($query) {
                $query->select('naziv_predstave', 'predstava_slug', 'predstavaid', 'plakat')
                    ->with(['zanrovi' => function ($query) {
                        $query->select('naziv_zanra', 'zanr_slug');
                    }]);
            }])
            ->with('scena')
            ->orderBy('seigraid', 'desc')
            ->take(50)
            ->get();
        return $igranja;
    }

    public function fetchSvaIgranjaFromDb()
    {
        $igranja = Igranje::with(['pozoriste' => function ($query) {
            $query->select('naziv_pozorista', 'pozoriste_slug');
        }])
            ->with(['predstava' => function ($query) {
                $query->select('naziv_predstave', 'predstava_slug', 'predstavaid', 'plakat')
                    ->with(['zanrovi' => function ($query) {
                        $query->select('naziv_zanra', 'zanr_slug');
                    }]);
            }])
            ->with('scena')
            ->orderBy('seigraid', 'desc')
            ->take(50)
            ->get();
        return $igranja;
    }

    public function getAllForGostovanja()
    {
        $pozorista = Pozoriste::select('pozoristeid', 'naziv_pozorista')->orderBy('naziv_pozorista')->get();
        $predstave = $this->vratiPredstaveZaDropdown2();
        $scene = Scena::select('scenaid', 'naziv_scene', 'pozoristeid')->get();
        $igranja = $this->fetchSvaIgranjaFromDb();
        $result = new stdClass();
        $result->pozorista = $pozorista;
        $result->predstave = $predstave;
        $result->scene = $scene;
        $result->igranja = $igranja;
        return json_encode($result);
    }

    public function vratiPredstaveZaDropdown2()
    {
        $predstave = Predstava::select('predstavaid', 'naziv_predstave')->with(['pozorista' => function ($query) {
            $query->select('naziv_pozorista', 'pozoriste.pozoristeid');
        }])->orderBy('naziv_predstave', 'asc')->get();

        $predstaveCounts = $predstave->groupBy('naziv_predstave')->map->count();

        foreach ($predstave as $pred) {
            if ($predstaveCounts[$pred->naziv_predstave] > 1) {
                $pred->naziv_predstave = $pred->naziv_predstave . ' - ' . $pred->pozorista->pluck('naziv_pozorista')->implode(', ');
            }
        }
        return $predstave;
    }

    public function vratiPredstaveZaDropdown()
    {
        $predstave = Predstava::select('predstavaid', 'naziv_predstave')->with(['pozorista' => function ($query) {
            $query->select('naziv_pozorista', 'pozoriste.pozoristeid');
        }])->orderBy('naziv_predstave', 'asc')->get();
        foreach ($predstave as $pred) {
            $brojNazivaPredstava = $this->izbrojNazivePredstava($pred);
            if ($brojNazivaPredstava > 1) {
                $pred->naziv_predstave = $pred->naziv_predstave . ' - ' . $pred->pozorista->pluck('naziv_pozorista')->implode(', ');
            }
        }
        return $predstave;
    }

    public function izbrojNazivePredstava($predstava)
    {
        $broj = Predstava::where('naziv_predstave', $predstava->naziv_predstave)->count();
        return $broj;
    }

    public function daLiJePozoristeSlobodno($igranje)
    {
        if ($igranje->scenaid == null) {
            $test = Igranje::where('pozoristeid', $igranje->pozoristeid)->where('datum', '=', $igranje->datum)->where('vreme', '=', $igranje->vreme)->exists();
        } else {
            $test = Igranje::where('scenaid', $igranje->scenaid)->where('datum', '=', $igranje->datum)->where('vreme', '=', $igranje->vreme)->exists();
        }
        return !$test;
    }

    public function daLiSePredstavaVecIgraNegde($igranje)
    {
        $test = Igranje::where('predstavaid', $igranje->predstavaid)->where('datum', '=', $igranje->datum)->where('vreme', '=', $igranje->vreme)->exists();
        return !$test;
    }
}
