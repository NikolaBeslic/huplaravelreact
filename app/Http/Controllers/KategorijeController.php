<?php

namespace App\Http\Controllers;

use App\Models\Kategorija;
use Illuminate\Http\Request;

class KategorijeController extends Controller
{
    //

    public function adminindex()
    {
        $kategorije = Kategorija::where('parent_kategorija', null)->with('subkategorije')->get();
        return json_encode($kategorije);
    }
}
