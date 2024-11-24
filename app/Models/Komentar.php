<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    //

    protected $table = 'komentar';
    protected $primaryKey = 'komentarid';

    public function predstave()
    {
        return $this->belongsTo(Predstava::class, 'predstavaid');
    }

    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'korisnikid');
    }
}
