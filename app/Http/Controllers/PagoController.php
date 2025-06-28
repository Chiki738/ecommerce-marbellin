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
        $pedido = \App\Models\Pedido::find($pedidoId);

        if ($pedido && $pedido->estado_id == 1) { // 1 = pendiente
            $pedido->estado_id = 2; // 2 = procesando
            $pedido->save();

            session()->flash('success', 'Tu pedido fue generado correctamente.');
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Pedido no encontrado o ya procesado']);
    }
}
