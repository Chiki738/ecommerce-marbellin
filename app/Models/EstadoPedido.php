<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
    protected $table = 'estado_pedido'; // nombre exacto de la tabla

    public $timestamps = false; // si no tienes columnas created_at / updated_at

    protected $fillable = ['nombre'];
}
