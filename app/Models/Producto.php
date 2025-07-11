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
        return $this->hasMany(VarianteProducto::class, 'producto_codigo', 'codigo');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'categoria_id');
    }

    protected static function booted(): void
    {
        static::deleting(fn(self $producto) => $producto->variantes()->delete());
    }
}
