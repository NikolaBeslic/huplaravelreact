<?php

namespace App\Http\Controllers;

use App\Models\Korisnik;
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
}
