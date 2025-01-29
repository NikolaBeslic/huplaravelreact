<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorija extends Model
{
    //
    protected $table = 'kategorija';
    protected $primaryKey = 'kategorijaid';
    protected $fillable = ['naziv_kategorije', 'kategorija_slug', 'kategorija_boja', 'parent_kategorija', 'display_naziv_kategorije'];
    public $timestamps = false;

    public function tekstovi()
    {
        return $this->hasMany(Tekst::class, 'kategorijaid')->with('kategorija')->orderBy('created_at', 'desc');
    }

    public function subkategorije()
    {
        return $this->hasMany(Kategorija::class, 'parent_kategorija');
    }
}
