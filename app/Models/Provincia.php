<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provincia extends Model
{
    protected $primaryKey = 'provincia_id';
    public $timestamps = false;

    public function distritos(): HasMany
    {
        return $this->hasMany(Distrito::class);
    }
}
