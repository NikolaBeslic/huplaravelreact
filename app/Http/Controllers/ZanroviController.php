<?php

namespace App\Http\Controllers;

use App\Models\Zanr;
use Illuminate\Http\Request;
use Dotenv\Exception\ValidationException;

class ZanroviController extends Controller
{
    //
    public function adminindex()
    {
        return json_encode(Zanr::all());
    }

    public function getSingleZanr($zanrid)
    {
        $zanr = Zanr::where('zanrid', $zanrid)->firstOrFail();
        return json_encode($zanr);
    }

    public function update(Request $request)
    {

        // 
        try {
            $request->validate([
                'naziv_zanra' => 'required',
                'zanr_slug' => 'required',
                'zanr_boja' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $zanr =  Zanr::where('zanrid', $request->zanrid)->firstOrFail();

        $zanr->fill($request->all());
        if ($zanr->save()) {
            return response()->json([], 200);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'naziv_zanra' => 'required',
                'zanr_slug' => 'required',
                'zanr_boja' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $zanr = new Zanr();

        $zanr->fill($request->all());
        if ($zanr->save()) {
            return response()->json([], 200);
        }
    }
}
