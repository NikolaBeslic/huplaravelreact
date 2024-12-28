<?php

namespace App\Http\Controllers;

use App\Models\Kategorija;
use Illuminate\Http\Request;
use Dotenv\Exception\ValidationException;

class KategorijeController extends Controller
{
    //

    public function adminindex()
    {
        $kategorije = Kategorija::where('parent_kategorija', null)->with('subkategorije')->get();
        return json_encode($kategorije);
    }

    public function getSingleKategorija($kategorijeid)
    {
        $kategorija = Kategorija::where('kategorijaid', $kategorijeid)->firstOrfail();
        return json_encode($kategorija);
    }

    public function getSveKategorije()
    {
        return json_encode(Kategorija::all());
    }

    public function update(Request $request)
    {
        // 
        try {
            $request->validate([
                'naziv_kategorije' => 'required',
                'kategorija_slug' => 'required',
                'kategorija_boja' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $kategorija =  Kategorija::where('kategorijaid', $request->kategorijaid)->firstOrFail();

        $kategorija->fill($request->all());
        if ($kategorija->save()) {
            return response()->json([], 200);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'naziv_kategorije' => 'required',
                'kategorija_slug' => 'required',
                'kategorija_boja' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $kategorija = new Kategorija();

        $kategorija->fill($request->all());
        if ($kategorija->save()) {
            return response()->json([], 200);
        }
    }
}
