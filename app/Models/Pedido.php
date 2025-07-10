<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetallePedido;
use App\Models\EstadoPedido;
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

    protected $casts = [
        'fecha' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id', 'cliente_id');
    }

    function estadoTexto($estado_id)
    {
        return match ($estado_id) {
            1 => 'Pendiente',
            2 => 'Procesando',
            3 => 'Enviado',
            4 => 'Entregado',
            5 => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function estado()
    {
        return $this->belongsTo(EstadoPedido::class, 'estado_id');
    }
}
