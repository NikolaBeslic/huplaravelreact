<?php

namespace App\Http\Controllers;

use App\Http\Resources\IgranjeResource;
use App\Http\Resources\PozoristeResource;
use App\Igranje;
use Illuminate\Http\Request;
use App\Models\Pozoriste;

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
        return json_encode($pozoriste);
        // return $pozoriste;
        //return IgranjeResource::collection($pozoriste->igranja);
        //return PozoristeResource::make($pozoriste);

    }
}
