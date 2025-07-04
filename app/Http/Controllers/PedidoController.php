<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\{Pedido, DetallePedido, Producto, VarianteProducto};

class PedidoController extends Controller
{
    public function agregarAlCarrito(Request $request)
    {
        if (!Auth::check()) {
            return $this->respuesta($request, 'Debes iniciar sesión', 401, true);
        }

        $request->validate([
            'producto_codigo' => 'required|string|exists:productos,codigo',
            'talla' => 'required|string',
            'color' => 'required|string',
            'cantidad' => 'required|integer|min:1',
        ]);

        try {
            $user = Auth::user();
            $producto = Producto::where('codigo', $request->producto_codigo)->firstOrFail();

            $variante = VarianteProducto::where([
                ['producto_codigo', $request->producto_codigo],
                ['talla', $request->talla],
                ['color', $request->color],
            ])->first();

            if (!$variante || $variante->cantidad < $request->cantidad) {
                return $this->respuesta($request, 'Talla/color no disponible o stock insuficiente', 422, true);
            }

            $pedido = Pedido::firstOrCreate(
                ['cliente_id' => $user->cliente_id, 'estado_id' => 1],
                [
                    'fecha' => now(),
                    'total' => 0,
                    'direccion_envio' => $user->direccion,
                    'distrito_id' => $user->distrito_id,
                ]
            );

            $detalle = DetallePedido::firstOrNew([
                'pedido_id' => $pedido->id,
                'producto_codigo' => $producto->codigo,
                'variante_id' => $variante->id,
            ]);

            $detalle->cantidad += $request->cantidad;
            $detalle->precio_unit = $producto->precio;
            $detalle->subtotal += $producto->precio * $request->cantidad;
            $detalle->save();

            $pedido->update(['total' => $pedido->detalles()->sum('subtotal')]);

            Log::info("Producto agregado al pedido ID: {$pedido->id}");

            return $this->respuesta($request, 'Producto agregado al carrito');
        } catch (\Exception $e) {
            Log::error("Error al agregar al carrito: {$e->getMessage()}");
            return $this->respuesta($request, 'Ocurrió un error al agregar el producto', 500, true);
        }
    }

    public function carrito()
    {
        $pedido = Pedido::with('detalles.producto', 'detalles.variante')
            ->where('cliente_id', Auth::user()->cliente_id)
            ->where('estado_id', 1)
            ->first();

        return view('carrito.carrito', compact('pedido'));
    }

    public function actualizarCantidad(Request $request, $id)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);

        $detalle = DetallePedido::findOrFail($id);
        $detalle->update([
            'cantidad' => $request->cantidad,
            'subtotal' => $detalle->precio_unit * $request->cantidad,
        ]);

        $detalle->pedido->update([
            'total' => $detalle->pedido->detalles()->sum('subtotal'),
        ]);

        return $this->respuesta($request, 'Cantidad actualizada correctamente');
    }

    public function eliminar(Request $request, $id)
    {
        $detalle = DetallePedido::findOrFail($id);
        $pedido = $detalle->pedido;

        $detalle->delete();
        $pedido->update(['total' => $pedido->detalles()->sum('subtotal')]);

        return $this->respuesta($request, 'Producto eliminado del carrito');
    }

    public function vaciar(Request $request)
    {
        $pedido = Pedido::with('detalles')
            ->where('cliente_id', Auth::user()->cliente_id)
            ->where('estado_id', 1)
            ->first();

        if ($pedido) {
            $pedido->detalles()->delete();
            $pedido->update(['total' => 0]);
        }

        return $this->respuesta($request, 'Carrito vaciado correctamente');
    }

    /**
     * Retorna respuesta JSON o redirect según tipo de request.
     */
    private function respuesta(Request $request, string $mensaje, int $code = 200, bool $esError = false)
    {
        if ($request->ajax()) {
            return response()->json([$esError ? 'error' : 'message' => $mensaje], $code);
        }

        $tipo = $esError ? 'error' : 'success';
        return redirect()->back()->with($tipo, $mensaje);
    }
}
