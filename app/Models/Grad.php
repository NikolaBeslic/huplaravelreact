<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grad extends Model
{
    //

    protected $table = 'grad';
    protected $primaryKey = 'gradid';
    protected $fillable = ['naziv_grada'];

    public function pozorista()
    {
        return $this->hasMany(Pozoriste::class, 'gradid');
    }
}
