<?php

namespace App\Http\Controllers;

use App\Models\Kategorija;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    function uploadImage(Request $request)
    {
        if ($request->file('slika')) {
            if ($request->kategorijaid) {
                $kategorija_slug = Kategorija::where('kategorijaid', $request->kategorijaid)->value('kategorija_slug');
            }
            $fileName = $request->file('slika')->getClientOriginalName();
            $path = $request->file('slika')->move(base_path() . '/react/public/slike/' . $kategorija_slug, $fileName);
            $location = '/slike/' . $kategorija_slug . '/' . $fileName;
            return response()->json(['location' => $location], 200);
        }
    }
}
