<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';
    protected $fillable = [
        'pedido_id',
        'producto_codigo',
        'talla',
        'color',
        'cantidad',
        'precio_unit',
        'subtotal',
        'variante_id', // ✅ aquí también
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_codigo', 'codigo');
    }

    public function variante()
    {
        return $this->belongsTo(\App\Models\VarianteProducto::class, 'variante_id');
    }
}
