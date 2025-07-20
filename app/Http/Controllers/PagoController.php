<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComprobantePagoMail;
use App\Models\{Pedido, User};

class PagoController extends Controller
{
    public function exito(Request $request)
    {
        $pedido = Pedido::with(['detalles.variante', 'detalles.producto', 'cliente'])
            ->find($request->get('pedido_id'));

        if (!$pedido || $pedido->estado_id !== 1) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado o ya procesado']);
        }

        $pedido->estado_id = 2; // Procesando
        $pedido->save();

        // Restar stock
        foreach ($pedido->detalles as $detalle) {
            if ($detalle->variante) {
                $detalle->variante->cantidad = max(0, $detalle->variante->cantidad - $detalle->cantidad);
                $detalle->variante->save();
            }
        }

        // Enviar comprobante
        if ($pedido->cliente?->email) {
            Mail::to($pedido->cliente->email)->send(new ComprobantePagoMail($pedido));
        }

        return response()->json(['success' => true]);
    }

    public function verificarStock($pedidoId)
    {
        $pedido = Pedido::with(['detalles.variante', 'detalles.producto'])->find($pedidoId);

        if (!$pedido) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado.']);
        }

        $errores = [];

        foreach ($pedido->detalles as $detalle) {
            $producto = $detalle->producto->nombre;

            if (!$detalle->variante) {
                $errores[] = "Producto '$producto' no tiene variante asociada.";
                continue;
            }

            $variante = $detalle->variante;
            $talla = $variante->talla;
            $color = $variante->color;

            if ($variante->cantidad <= 0) {
                $errores[] = "Producto '$producto' con talla '$talla' y color '$color' tiene stock agotado.";
            } elseif ($detalle->cantidad > $variante->cantidad) {
                $errores[] = "Producto '$producto' con talla '$talla' y color '$color' tiene stock insuficiente.";
            }
        }


        return empty($errores)
            ? response()->json(['success' => true])
            : response()->json(['success' => false, 'errores' => $errores]);
    }
}
