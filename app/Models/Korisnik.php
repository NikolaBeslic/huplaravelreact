<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Korisnik extends Authenticatable
{
    use HasApiTokens, Notifiable;
    //
    protected $table = 'korisnik';

    protected $fillable = [
        'korisnicko_ime', 'email', 'password', 'provider', 'provider_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function listaZelja()
    {
        return $this->belongsToMany(Predstava::class, 'listazelja', 'korisnikid', 'predstavaid')->where('statuszeljeid', 1);
    }

    public function listaOdgledanih()
    {
        return $this->belongsToMany(Predstava::class, 'listazelja', 'korisnikid', 'predstavaid')->where('statuszeljeid', 2);
    }

    public function omiljenaPozorista()
    {
        return $this->belongsToMany(Pozoriste::class, 'omiljena_pozorista', 'korisnikid', 'pozoristeid');
    }

    public function komentari()
    {
        return $this->hasMany(Komentar::class, 'korisnikid');
    }
}
