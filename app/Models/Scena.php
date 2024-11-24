<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scena extends Model
{
    //
    protected $table = 'scena';
    protected $primaryKey = 'scenaid';

    public function pozoriste()
    {
        return $this->belongsTo(Pozoriste::class, 'pozoristeid');
    }
}
