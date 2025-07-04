<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PagoController extends Controller
{
    public function exito(Request $request)
    {
        $pedidoId = $request->get('pedido_id');

        if (!$pedidoId) {
            return response()->json(['success' => false, 'message' => 'ID de pedido no proporcionado']);
        }

        // Buscar el pedido y actualizar estado
        $pedido = \App\Models\Pedido::with('detalles.variante')->find($pedidoId);

        if ($pedido && $pedido->estado_id == 1) { // 1 = pendiente
            $pedido->estado_id = 2; // 2 = procesando
            $pedido->save();

            // Restar stock a cada variante del pedido
            foreach ($pedido->detalles as $detalle) {
                $variante = $detalle->variante;
                if ($variante) {
                    $variante->cantidad -= $detalle->cantidad;
                    if ($variante->cantidad < 0) $variante->cantidad = 0;
                    $variante->save();
                }
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Pedido no encontrado o ya procesado']);
    }

    public function verificarStock($pedidoId)
    {
        $pedido = \App\Models\Pedido::with('detalles.variante', 'detalles.producto')->find($pedidoId);

        if (!$pedido) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado.']);
        }

        $errores = [];

        foreach ($pedido->detalles as $detalle) {
            $variante = $detalle->variante;
            if (!$variante) continue;

            if ($variante->cantidad <= 0) {
                $errores[] = "Producto '{$detalle->producto->nombre}' con talla '{$variante->talla}' y color '{$variante->color}' tiene stock agotado.";
            } elseif ($detalle->cantidad > $variante->cantidad) {
                $errores[] = "Producto '{$detalle->producto->nombre}' con talla '{$variante->talla}' y color '{$variante->color}' tiene stock insuficiente.";
            }
        }

        if (!empty($errores)) {
            return response()->json(['success' => false, 'errores' => $errores]);
        }

        return response()->json(['success' => true]);
    }
}
