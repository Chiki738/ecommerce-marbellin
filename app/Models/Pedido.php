<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetallePedido;

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

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id', 'cliente_id');
    }
}
