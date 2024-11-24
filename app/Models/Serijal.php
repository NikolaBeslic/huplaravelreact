<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serijal extends Model
{
    //

    protected $table = 'serijal';
    protected $primaryKey = 'serijalid';

    public function tekstovi()
    {
        return $this->hasMany(Tekst::class, 'serijalid');
    }
}
