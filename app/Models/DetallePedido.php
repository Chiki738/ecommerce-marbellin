<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';
    protected $fillable = ['pedido_id', 'producto_codigo', 'talla', 'color', 'cantidad', 'precio_unit', 'subtotal'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
