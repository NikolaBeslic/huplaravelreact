<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    //
    protected $table = 'autor';
    protected $primaryKey = 'autorid';
    protected $fillable = ['ime_autora', 'autor_slug', 'pozicija', 'biografija', 'url_slike', 'gradid'];

    public function tekstovi()
    {
        return $this->belongsToMany(Tekst::class, 'autor_tekst', 'autorid', 'tekstid');
    }

    public function grad()
    {
        return $this->belongsTo(Grad::class, 'gradid');
    }

    public function fotke()
    {
        return $this->hasMany(Fotografija::class, 'autorid');
    }
}
