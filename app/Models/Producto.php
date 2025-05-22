<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Variante;

class Producto extends Model
{
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['codigo', 'nombre', 'precio', 'descripcion', 'imagen', 'categoria'];

    public function variantes(): HasMany
    {
        return $this->hasMany(VarianteProducto::class, 'producto_codigo', 'codigo');
    }

    protected static function booted()
    {
        static::deleting(function ($producto) {
            $producto->variantes()->delete();
        });
    }
}
