<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'categoria_id'; // ✅ ahora está dentro de la clase

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
