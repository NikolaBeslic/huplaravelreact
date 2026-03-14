<?php

namespace App\Models;

use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Korisnik extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;
    //
    protected $table = 'korisnik';

    protected $fillable = [
        'korisnicko_ime',
        'email',
        'password',
        'provider',
        'provider_id',
        'email_verified_at'
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
                'pozorista:pozoristeid,naziv_pozorista,skraceni_naziv',
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
            ->with('komentarKorisnika')
            ->with([
                'pozorista:pozoristeid,naziv_pozorista,pozoriste_slug'
            ])
            ->with('zanrovi');
    }

    public function ocena()
    {
        return $this->hasMany(Ocena::class, 'korisnikid');
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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom);
    }
}
