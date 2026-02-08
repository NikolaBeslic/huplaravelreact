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
        'korisnicko_ime',
        'email',
        'password',
        'provider',
        'provider_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function listaZelja()
    {
        return $this->belongsToMany(
            Predstava::class,
            'listazelja',
            'korisnikid',
            'predstavaid'
        )
            ->select('predstava.predstavaid', 'naziv_predstave', 'predstava_slug', 'plakat')
            ->wherePivot('statuszeljeid', 1)
            ->with([
                'pozorista:pozoristeid,naziv_pozorista',
                'narednoIgranje'
            ]);
    }

    public function listaOdgledanih()
    {
        return $this->belongsToMany(
            Predstava::class,
            'listazelja',
            'korisnikid',
            'predstavaid'
        )
            ->select('predstava.predstavaid', 'naziv_predstave', 'predstava_slug', 'plakat')
            ->wherePivot('statuszeljeid', 2)
            ->with('ocenaKorisnika')
            ->with([
                'pozorista:pozoristeid,naziv_pozorista,pozoriste_slug'
            ]);
    }

    public function omiljenaPozorista()
    {
        return $this->belongsToMany(Pozoriste::class, 'omiljena_pozorista', 'korisnikid', 'pozoristeid')
            ->select('pozoriste.pozoristeid', 'naziv_pozorista', 'pozoriste_slug', 'url_logo', 'gradid')
            ->with('grad');
    }

    public function komentari()
    {
        return $this->hasMany(Komentar::class, 'korisnikid')
            ->select('komentarid', 'korisnikid', 'predstavaid', 'tekst_komentara', 'created_at')
            ->with(['predstava:predstavaid,naziv_predstave,predstava_slug']);
    }
}
