<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zanr extends Model
{
    //
    protected $table = 'zanr';
    protected $primaryKey = 'zanrid';
    protected $guarded = [];

    public function predstave()
    {
        return $this->belongsToMany(Predstava::class, 'pripadazanru', 'zanrid', 'predstavaid');
    }
}
