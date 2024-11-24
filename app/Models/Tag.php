<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //
    protected $table = 'tag';
    protected $primaryKey = 'tagid';

    public function tekstovi()
    {
        return $this->belongsToMany(Tekst::class, 'tekst_tag', 'tagid', 'tekstid');
    }
}
