<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tekst extends Model
{
    //
    protected $table = 'tekst';
    protected $primaryKey = 'tekstid';
    protected $fillable = ['naslov', 'sadrzaj', 'kategorijaid', 'slug', 'uvod', 'tekst_thumbnail', 'tekst_photo', 'na_slajderu', 'is_published', 'published_at', 'is_deleted', 'festivalid'];
    protected $guarded = [];
    protected $dates = ['created_at', 'published_at'];

    public function kategorija()
    {
        return $this->belongsTo(Kategorija::class, 'kategorijaid');
    }

    public function pozoriste()
    {
        return $this->belongsTo(Pozoriste::class, 'pozoristeid');
    }

    public function festival()
    {
        return $this->belongsTo(Festival::class, 'festivalid');
    }

    public function predstave()
    {
        return $this->belongsToMany(Predstava::class, 'tekst_predstava', 'tekstid', 'predstavaid');
    }

    public function pozorista()
    {
        return $this->belongsToMany(Pozoriste::class, 'tekst_pozoriste', 'tekstid', 'pozoristeid');
    }

    public function autori()
    {
        return $this->belongsToMany(Autor::class, 'autor_tekst', 'tekstid', 'autorid');
    }

    public function tagovi()
    {
        return $this->belongsToMany(Tag::class, 'tekst_tag', 'tekstid', 'tagid');
    }

    public function hupikon()
    {
        return $this->hasOne(Hupikon::class, 'tekstid');
    }

    public function serijal()
    {
        return $this->belongsTo(Serijal::class, 'kategorijaid');
    }

    public function hupkast()
    {
        return $this->hasOne(Hupkast::class, 'tekstid');
    }
}
