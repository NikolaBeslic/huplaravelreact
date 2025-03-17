<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hupikon extends Model
{
    //

    protected $table = 'hupikon';
    protected $primaryKey = 'hupikonid';
    protected $fillable = ['sagovornik', 'hupikon_slug', 'mesto_stanovanja', 'zanimanje_sagovornika', 'autorid', 'biografija', 'naslov_hupikona'];
    protected $guarded = [];

    public function tekst()
    {
        return $this->belongsTo(Tekst::class, 'tekstid');
    }
}
