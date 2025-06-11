<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = [
        'cliente_id',
        'fecha',
        'total',
        'direccion_envio',
        'distrito_id',
        'estado_id',
    ];

    // App\Models\Pedido.php
    public function detalles()
    {
        return $this->hasMany(\App\Models\DetallePedido::class, 'pedido_id');
    }
}
