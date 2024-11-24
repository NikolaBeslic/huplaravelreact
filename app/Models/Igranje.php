<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Igranje extends Model
{
    //

    protected $table = 'seigra';
    protected $primaryKey = 'seigraid';

    protected $fillable = ['pozoristeid', 'predstavaid', 'scenaid', 'datum', 'vreme', 'cena'];

    // public function predstava()
    // {
    //     return $this->hasMany(Predstava::class, 'predstavaid');
    // }

    public function predstava()
    {
        return $this->belongsTo(Predstava::class, 'predstavaid');
    }

    public function pozoriste()
    {
        return $this->belongsTo(Pozoriste::class, 'pozoristeid');
    }

    public function scena()
    {
        return $this->belongsTo(Scena::class, 'scenaid');
    }
}
