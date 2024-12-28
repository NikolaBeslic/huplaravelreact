<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Http\Resources\AutorResource;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;

class AutoriController extends Controller
{
    //
    public function getAutori()
    {
        $autori = Autor::where('status_autora', 1)->orderBy('ime_autora')->get();
        return AutorResource::collection($autori);
    }

    public function getSingleAutor($autor_slug)
    {
        $autor = Autor::where('autor_slug', $autor_slug)
            ->with('grad')
            ->with([
                'tekstovi' => function ($query) {
                    $query->select('tekst.tekstid', 'naslov', 'slug', 'uvod', 'tekst_thumbnail', 'tekst_photo', 'published_at', 'created_at', 'kategorijaid')
                        ->with(
                            'kategorija'
                        )->orderBy('published_at', 'desc')->paginate(10);
                }
            ])
            ->firstOrFail();
        return json_encode($autor);
    }

    public function getAllAutori()
    {
        return json_encode(Autor::where('status_autora', 1)->get());
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
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $autor = new Autor($request->all());
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
