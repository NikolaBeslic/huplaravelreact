<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ocena extends Model
{
    //

    protected $table = 'ocenio';
    protected $fillable = ['predstavaid', 'korisnikid', 'ocena'];
}
