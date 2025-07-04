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
        'variante_id',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_codigo', 'codigo');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }
}
