<?php

namespace App\Http\Controllers;

use App\Models\Igranje;
use App\Models\Pozoriste;
use App\Models\Zanr;
use App\Models\Predstava;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
}
