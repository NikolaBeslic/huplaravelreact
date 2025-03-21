<?php

namespace App\Http\Controllers;

use App\Models\Igranje;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;

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
        if ($igranje->save()) {
            $igranjaPozorista = $this->fetchIgranjaFromDb($request->pozoristeid);
            return response()->json($igranjaPozorista);
        }
        return response()->json('greska prilikom cuvanja izvodjenja', 500);
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
}
