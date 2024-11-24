<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Predstava extends Model
{
    //
    protected $table = 'predstava';
    protected $primaryKey = 'predstavaid';

    protected $guarded = ['created_at', 'published_at'];
    protected $fillable = ['naziv_predstave', 'predstava_slug', 'premijera', 'autor', 'reditelj', 'uloge', 'opis', 'plakat', 'updated_at', 'created_at'];

    public function pozorista()
    {
        return $this->belongsToMany(Pozoriste::class, 'predstava_pozoriste', 'predstavaid', 'pozoristeid');
    }

    public function igranja()
    {
        return $this->hasMany(Igranje::class, 'predstavaid');
    }

    public function zanrovi()
    {
        return $this->belongsToMany(Zanr::class, 'pripadazanru', 'predstavaid', 'zanrid');
    }

    public function tekstovi()
    {
        return $this->belongsToMany(Tekst::class, 'tekst_predstava', 'predstavaid', 'tekstid');
    }

    public function komentari()
    {
        return $this->hasMany(Komentar::class, 'predstavaid');
    }

    public function brojKomentara()
    {
        return $this->hasMany(Komentar::class, 'predstavaid')->count();
    }

    public function naListiZelja()
    {
        return $this->belongsToMany(Korisnik::class, 'listazelja', 'predstavaid', 'korisnikid')->where('statuszeljeid', 1);
    }

    public function naListiOdgledanih()
    {
        return $this->belongsToMany(Korisnik::class, 'listazelja', 'predstavaid', 'korisnikid')->where('statuszeljeid', 2);
    }

    public function ocena()
    {
        return $this->hasMany(Ocena::class, 'predstavaid');
    }

    public function ocenaKorisnika()
    {
        return $this->hasOne(Ocena::class, 'predstavaid')->where('korisnikid', Auth::id());
    }

    public function narednoIgranje()
    {
        return $this->hasOne(Igranje::class, 'predstavaid');
    }
}
