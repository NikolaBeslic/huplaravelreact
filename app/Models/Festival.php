<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    //
    protected $table = 'festival';
    protected $primaryKey = 'festivalid';

    protected $fillable = ['gradid', 'datumod', 'datumdo', 'naziv_festivala', 'festival_slug', 'festival_slika', 'tekst_festivala', 'repertoar'];
    protected $guarded = ['created_at', 'published_at'];

    public function grad()
    {
        return $this->belongsTo(Grad::class, 'gradid');
    }

    public function tekstovi()
    {
        return $this->hasMany(Tekst::class, 'festivalid')->orderBy('tekst.published_at', 'desc');
    }
}
