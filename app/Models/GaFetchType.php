<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaFetchType extends Model
{
    //
    protected $table = 'ga_fetch_type';
    protected $primaryKey = 'type_id';
    public $timestamps = false;

    public function fetches()
    {
        return $this->hasMany(GaFetch::class, 'type_id');
    }
}
