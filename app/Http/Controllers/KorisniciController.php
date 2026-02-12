<?php

namespace App\Http\Controllers;

use App\Models\Korisnik;
use App\Models\Predstava;
use Illuminate\Http\Request;

class KorisniciController extends Controller
{

    public function getKorisnickiProfil()
    {
        $korisnik = Korisnik::where('id', auth('sanctum')->user()->id)
            ->with('listaZelja')
            ->with('listaOdgledanih')
            ->with('komentari')
            ->with('omiljenaPozorista')
            ->firstOrFail();
        return json_encode($korisnik);
    }

    public function obrisiSaListeZelja(Request $request)
    {
        $predstava = Predstava::where('predstavaid', $request->predstavaid)
            ->firstOrFail();
        $user = auth('sanctum')->user();
        if ($predstava->naListiZelja()->detach(auth('sanctum')->user()->id))
            return response()->json($predstava->predstavaid);
    }
}
