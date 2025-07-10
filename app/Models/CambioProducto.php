<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CambioProducto extends Model
{
    use HasFactory;

    protected $table = 'cambios_productos'; // Asegura que apunte a tu tabla

    protected $fillable = [
        'pedido_id',
        'detalle_pedido_id',
        'variante_antigua_id',
        'variante_nueva_id',
        'estado',
        'comentario_cliente',
        'comentario_admin'
    ];

    // Relaciones (opcional pero útil)
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function detalle()
    {
        return $this->belongsTo(DetallePedido::class, 'detalle_pedido_id');
    }

    public function varianteAntigua()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_antigua_id');
    }

    public function varianteNueva()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_nueva_id');
    }
}
