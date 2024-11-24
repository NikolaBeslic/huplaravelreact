<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hupkast extends Model
{
    //

    protected $table = 'hupkast';
    protected $primaryKey = 'hupkastid';
    protected $fillable = ['hupkastid'];
    protected $guarded = [];

    public function tekst()
    {
        return $this->belongsTo(Tekst::class, 'tekstid');
    }

    public function linkovi()
    {
        return $this->belongsToMany(HupkastPlatforme::class, 'hupkast_link', 'hupkastid', 'platformaid')->withPivot('hupkast_url');
    }
}

class HupkastPlatforme extends Model
{
    protected $table = 'hupkast_platforme';
    protected $primaryKey = 'platformaid';
    protected $fillable = ['hupkastid', 'platformaid', 'platforma_icon', 'hupkast_url'];

    public function hupkasti()
    {
        return $this->belongsToMany(HuPkast::class, 'hupkast_link', 'platformaid', 'hupkastid')->withPivot('hupkast_url');
    }
}
