<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $primaryKey = 'distrito_id';
    public $timestamps = false;

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }
}
