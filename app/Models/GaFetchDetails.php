<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaFetchDetails extends Model
{
    //
    protected $table = 'ga_fetch_details';
    protected $primaryKey = 'fetch_details_id';
    protected $fillable = ['fetch_id', 'title', 'url', 'views'];
    public $timestamps = false;



    public function fetch()
    {
        return $this->belongsTo(GaFetch::class, 'fetch_id');
    }
}
