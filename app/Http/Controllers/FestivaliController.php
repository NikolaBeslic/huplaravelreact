<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use Illuminate\Http\Request;
use Dotenv\Exception\ValidationException;

class FestivaliController extends Controller
{
    //
    public function getFestivali()
    {

        $festivali = Festival::select('festivalid', 'gradid', 'naziv_festivala', 'festival_slug', 'festival_slika', 'datumod', 'datumdo')
            ->with('grad')
            ->orderBy('datumod', 'desc')
            ->paginate(12);
        return json_encode($festivali);
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

    public function adminGetFestivaliZaNaslovnu()
    {
        $festivali = Festival::with('grad')->orderBy('created_at', 'desc')->take(7)->get();
        return json_encode($festivali);
    }

    public function adminGetAllFestivali()
    {
        $festivali = Festival::select('festivalid', 'gradid', 'naziv_festivala',  'datumod', 'datumdo')
            ->with('grad')
            ->orderBy('datumod', 'desc')
            ->get();
        return json_encode($festivali);
    }

    public function getSingleFestivalAdmin($festivalid)
    {
        $festival = Festival::where('festivalid', $festivalid)->firstOrFail();
        return json_encode($festival);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'naziv_festivala' => 'required',
                'festival_slug' => 'required|unique:festival',
                'datumdo' => 'required',
                'datumdo' => 'required',
                'gradid' => 'required'
                //'slika' => 'image|mimes:jpeg,png,jpg'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $festival = new Festival($request->all());

        if ($request->file('festival_slika')) {
            $fileExtension = $request->file('festival_slika')->extension();
            $fileName = $request->festival_slug . '.' . $fileExtension;
            $path = $request->file('festival_slika')->move(base_path() . '/react/public/slike/festivali', $fileName);
            $festival->festival_slika = '/slike/festivali/' . $fileName;
        }
        if ($festival->save())
            return response()->json([], 200);
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'naziv_festivala' => 'required',
                'festival_slug' => 'required',
                'datumdo' => 'required',
                'datumdo' => 'required',
                'gradid' => 'required'
                //'slika' => 'image|mimes:jpeg,png,jpg'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $festival = Festival::where('festivalid', $request->festivalid)->firstOrFail();
        $festival = $festival->fill($request->all());
        if ($request->file('festival_slika')) {
            $fileExtension = $request->file('festival_slika')->extension();
            $fileName = $request->festival_slug . '.' . $fileExtension;
            $path = $request->file('festival_slika')->move(base_path() . '/react/public/slike/festivali', $fileName);
            $festival->festival_slika = '/slike/festivali/' . $fileName;
        }
        if ($festival->save())
            return response()->json([], 200);
    }
}
