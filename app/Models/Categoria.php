<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $primaryKey = 'categoria_id';

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id', 'categoria_id');
    }
}
