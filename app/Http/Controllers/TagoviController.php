<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagoviController extends Controller
{
    //
    public function getAllTagovi()
    {
        $tagovi = Tag::withCount('tekstovi')->get();
        return json_encode($tagovi);
    }

    public function storeTags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.tag_naziv' => 'required|unique:tag',
            '*.tag_slug' => 'required|unique:tag'
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);
        // try {

        //     $request->validate([
        //         'tag_naziv' => 'required|unique:tag',
        //         'tag_slug' => 'required|unique:tag'
        //     ]);
        // } catch (ValidationException $e) {
        //     return response()->json($e, 422);
        // }

        if (Tag::insert($request->all()))
            $tagovi = Tag::withCount('tekstovi')->get();
        return response()->json($tagovi, 200);
    }

    public function getTekstsByTag($tag_slug)
    {
        $tekstovi = Tag::where('tag_slug', $tag_slug)->with([
            'tekstovi' => function ($query) {
                $query->select('tekst.tekstid', 'kategorijaid', 'naslov', 'slug', 'tekst_photo', 'uvod');
            },
            'tekstovi.kategorija'
        ])->firstOrFail();
        return json_encode($tekstovi);
    }
}
