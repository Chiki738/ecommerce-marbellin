<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'nombre',
        'precio',
        'descripcion',
        'imagen',
        'categoria_id',
    ];

    public function variantes(): HasMany
    {
        return $this->hasMany(VarianteProducto::class, 'producto_codigo');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $producto) {
            $producto->variantes()->delete();
        });
    }
}
