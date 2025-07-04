<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $primaryKey = 'provincia_id';
    public $timestamps = false;

    public function distritos()
    {
        return $this->hasMany(Distrito::class);
    }
}
