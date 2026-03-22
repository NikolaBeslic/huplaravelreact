<?php

namespace App\Http\Controllers;

use App\Models\Korisnik;
use App\Models\Predstava;
use Illuminate\Http\Request;

class KorisniciController extends Controller
{

    public function getKorisnickiProfil()
    {
        $korisnik = Korisnik::where('id', auth('sanctum')->id())
            ->with('omiljenaPozorista')
            ->withCount([
                'listaZelja as broj_liste_zelja',
                'listaOdgledanih as broj_odgledanih',
                'komentari as broj_komentara',
            ])
            ->withAggregate(['ocena as prosecna_ocena'], 'ROUND(AVG(ocena), 1)')
            ->firstOrFail();
        return json_encode($korisnik);
    }

    public function getListaZelja(Request $request)
    {
        $korisnik = Korisnik::findOrFail(auth('sanctum')->id());
        $initialCount = 30; // First page load
        $subsequentCount = 9; // Page 2 and beyond

        $page = $request->get('page', 1);
        $perPage = ($page == 1) ? $initialCount : $subsequentCount;
        $listaZelja = $korisnik->listaZelja()
            ->paginate($perPage);

        return response()->json($listaZelja);
    }

    public function getListaOdgledanih(Request $request)
    {
        $korisnik = Korisnik::findOrFail(auth('sanctum')->id());
        $initialCount = 20; // First page load
        $subsequentCount = 10; // Page 2 and beyond

        $page = $request->get('page', 1);
        $perPage = ($page == 1) ? $initialCount : $subsequentCount;
        $listaOdgledanih = $korisnik->listaOdgledanih()
            ->paginate($perPage);

        return response()->json($listaOdgledanih);
    }

    public function getKorisnikKomentari(Request $request)
    {
        $korisnik = Korisnik::findOrFail(auth('sanctum')->id());

        $komentari = $korisnik->komentari()
            ->paginate(15);

        return response()->json($komentari);
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
