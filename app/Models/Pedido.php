<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = [
        'cliente_id',
        'fecha',
        'estado',
        'total',
        'direccion_envio',
        'distrito',      // ✅ AÑADIR
        'provincia',     // ✅ AÑADIR
    ];


    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
}
