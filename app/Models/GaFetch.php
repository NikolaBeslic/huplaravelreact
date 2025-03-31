<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaFetch extends Model
{
    //
    protected $table = 'ga_fetch';
    protected $primaryKey = 'fetch_id';
    protected $fillable = ['typeid', 'parameter', 'error_msg'];

    public function fetchType()
    {
        return $this->belongsTo(GaFetchType::class, 'type_id');
    }

    public function fetchDetails()
    {
        return $this->hasMany(GaFetchDetails::class, 'fetch_id');
    }
}
