<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Http\Resources\AutorResource;
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
}
