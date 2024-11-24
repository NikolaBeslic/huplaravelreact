<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pozoriste extends Model
{
    //
    protected $table = 'pozoriste';
    protected $primaryKey = 'pozoristeid';

    protected $guarded = ['created_at', 'published_at'];

    public function grad()
    {
        return $this->belongsTo(Grad::class, 'gradid');
    }

    public function scene()
    {
        return $this->hasMany(Scena::class, 'pozoristeid');
    }

    public function predstave()
    {
        return $this->belongsToMany(Predstava::class, 'predstava_pozoriste', 'pozoristeid', 'predstavaid');
    }

    public function igranja()
    {
        return $this->hasMany(Igranje::class, 'pozoristeid');
        // return $this->belongsToMany(Predstava::class, 'seigra', 'predstavaid', 'pozoristeid');
    }

    public function tekstovi()
    {
        return $this->belongsToMany(Tekst::class, 'tekst_pozoriste', 'pozoristeid', 'tekstid')->orderBy('tekst.published_at', 'desc');
    }
}
