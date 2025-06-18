<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Http\Resources\AutorResource;
use App\Models\Tekst;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutoriController extends Controller
{
    //
    public function getAutori()
    {
        $autori = Autor::where('status_autora', 1)->orderBy('ime_autora')->get();

        return json_encode($autori);
    }

    public function getSingleAutor($autor_slug)
    {
        $autor = Autor::where('autor_slug', $autor_slug)
            ->with('grad')
            ->firstOrFail();

        $tekstovi = $autor->tekstovi()
            ->select('tekst.tekstid', 'naslov', 'slug', 'uvod', 'tekst_photo', 'tekst.kategorijaid', 'published_at')
            ->with('kategorija')
            ->where('is_published', 1)
            ->orderBy('published_at', 'desc')
            ->paginate(12);


        $autor->setRelation('tekstovi', $tekstovi);
        return json_encode($autor);
    }

    public function getAllAutori()
    {
        $autori = Autor::where('status_autora', 1)->get();
        return json_encode($autori);
    }

    public function getSingleAutorAdmin($autorid)
    {
        return json_encode(Autor::where('autorid', $autorid)->firstOrFail());
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ime_autora' => 'required',
                'autor_slug' => 'required|unique:autor',
                'pozicija' => 'required',
                //'slika' => 'image|mimes:jpeg,png,jpg'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $autor = new Autor($request->all());

        if ($request->file('slika')) {
            $fileExtension = $request->file('slika')->extension();
            $fileName = $request->autor_slug . '.' . $fileExtension;
            $path = $request->file('slika')->move(base_path() . '/react/public/slike/autori', $fileName);
            $autor->url_slike = '/slike/autori/' . $fileName;
        }
        if ($autor->save())
            return response()->json([], 200);
    }

    public function update(Request $request)
    {
        $autor = Autor::where('autorid', $request->autorid)->firstOrFail();
        try {
            $request->validate([
                'ime_autora' => 'required',

                'pozicija' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }
        $autor = $autor->fill($request->all());
        if ($autor->save())
            return response()->json([], 200);
    }
}
