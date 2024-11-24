<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fotografija extends Model
{
    //
    protected $table = 'fotografija';
    protected $primaryKey = 'fotografijaid';

    protected $guarded = ['created_at', 'published_at'];

    public function autor()
    {
        return $this->belongsTo(Autor::class, 'autorid');
    }
}
