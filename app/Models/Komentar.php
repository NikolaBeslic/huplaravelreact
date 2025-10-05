<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    //

    protected $table = 'komentar';
    protected $primaryKey = 'komentarid';

    public function predstava()
    {
        return $this->belongsTo(Predstava::class, 'predstavaid');
    }

    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'korisnikid');
    }
}
