<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use Illuminate\Http\Request;

class FestivaliController extends Controller
{
    //
    public function getFestivali()
    {
        return json_encode(Festival::with('grad')->orderBy('datumod', 'desc')->take(30)->get());
    }

    public function getAllFestivali()
    {
        $festivali = Festival::select('festivalid', 'naziv_festivala')->orderBy('datumOd')->get();
        return json_encode($festivali);
    }

    public function getSingleFestival($festival_slug)
    {
        $festival = Festival::where('festival_slug', $festival_slug)->with('grad')->with('tekstovi.kategorija')
            ->firstOrFail();
        return json_encode($festival);
    }
}
