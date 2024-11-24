<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagoviController extends Controller
{
    //
    public function getAllTagovi()
    {
        $tagovi = Tag::all();
        return json_encode($tagovi);
    }
}
