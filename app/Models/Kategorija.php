<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorija extends Model
{
    //
    protected $table = 'kategorija';
    protected $primaryKey = 'kategorijaid';

    public function tekstovi()
    {
        return $this->hasMany(Tekst::class, 'kategorijaid')->with('kategorija')->orderBy('created_at', 'desc');
    }

    public function subkategorije()
    {
        return $this->hasMany(Kategorija::class, 'parent_kategorija');
    }
}
