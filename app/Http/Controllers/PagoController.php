<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\ComprobantePagoMail;
use App\Models\{Pedido, VarianteProducto};

class PagoController extends Controller
{
    public function exito(Request $request)
    {
        $data = $request->validate([
            'pedido_id' => ['required', 'integer'],
        ]);

        try {
            $pedido = DB::transaction(function () use ($data) {
                $pedido = Pedido::with(['detalles.producto', 'cliente'])
                    ->where('cliente_id', Auth::user()->cliente_id)
                    ->where('estado_id', 1)
                    ->lockForUpdate()
                    ->find($data['pedido_id']);

                if (!$pedido) {
                    throw ValidationException::withMessages([
                        'pedido_id' => 'Pedido no encontrado o ya procesado.',
                    ]);
                }

                foreach ($pedido->detalles as $detalle) {
                    $variante = VarianteProducto::whereKey($detalle->variante_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$variante || $detalle->cantidad > $variante->cantidad) {
                        throw ValidationException::withMessages([
                            'stock' => "Stock insuficiente para {$detalle->producto->nombre}.",
                        ]);
                    }

                    $variante->decrement('cantidad', $detalle->cantidad);
                }

                $pedido->update(['estado_id' => 2]);

                return $pedido->load(['detalles.producto', 'detalles.variante', 'cliente']);
            });
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first(),
            ], 422);
        }

        if ($pedido->cliente?->email) {
            Mail::to($pedido->cliente->email)->send(new ComprobantePagoMail($pedido));
        }

        return response()->json(['success' => true]);
    }

    public function verificarStock($pedidoId)
    {
        $pedido = Pedido::with(['detalles.variante', 'detalles.producto'])
            ->where('cliente_id', Auth::user()->cliente_id)
            ->where('estado_id', 1)
            ->find($pedidoId);

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
