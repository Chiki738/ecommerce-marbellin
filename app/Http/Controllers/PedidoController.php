<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;

class PedidoController extends Controller
{
    public function agregarAlCarrito(Request $request)
    {
        // Si no está logueado, redirige al login
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Debes iniciar sesión'], 401);
            }
            return redirect()->route('login');
        }


        // Validar datos del formulario
        $request->validate([
            'producto_codigo' => 'required|string|exists:productos,codigo',
            'talla' => 'required|string',
            'color' => 'required|string',
            'cantidad' => 'required|integer|min:1',
        ]);

        try {
            $user = Auth::user();
            Log::info("Cliente autenticado: " . $user->cliente_id);

            $producto = Producto::where('codigo', $request->producto_codigo)->firstOrFail();

            $variante = \App\Models\VarianteProducto::where([
                ['producto_codigo', '=', $request->producto_codigo],
                ['talla', '=', $request->talla],
                ['color', '=', $request->color],
            ])->first();

            if (!$variante || $variante->cantidad < $request->cantidad) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'La combinación de talla y color no está disponible o no hay suficiente stock.'], 422);
                }
                return redirect()->back()->with('error', 'La combinación de talla y color no está disponible o no hay suficiente stock.');
            }

            $distrito = $user->distrito;
            $provincia = $distrito?->provincia;

            $pedido = Pedido::firstOrCreate(
                ['cliente_id' => $user->cliente_id, 'estado_id' => 1],
                [
                    'fecha' => now(),
                    'total' => 0,
                    'direccion_envio' => $user->direccion,
                    'distrito_id' => $user->distrito_id,
                ]
            );

            $precioUnit = $producto->precio;
            $subtotal = $precioUnit * $request->cantidad;

            $detalleExistente = DetallePedido::where('pedido_id', $pedido->id)
                ->where('producto_codigo', $producto->codigo)
                ->where('variante_id', $variante->id)
                ->first();

            if ($detalleExistente) {
                $detalleExistente->cantidad += $request->cantidad;
                $detalleExistente->subtotal += $precioUnit * $request->cantidad;
                $detalleExistente->save();
            } else {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_codigo' => $producto->codigo,
                    'cantidad' => $request->cantidad,
                    'precio_unit' => $precioUnit,
                    'subtotal' => $subtotal,
                    'variante_id' => $variante->id,
                ]);
            }

            $pedido->total += $subtotal;
            $pedido->save();

            Log::info("Producto agregado al pedido ID: " . $pedido->id);

            if ($request->ajax()) {
                return response()->json(['message' => 'Producto agregado al carrito']);
            }

            return redirect()->back()->with('success', 'Producto agregado al carrito');
        } catch (\Exception $e) {
            Log::error("Error al agregar al carrito: " . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Ocurrió un error al agregar el producto'], 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error al agregar el producto');
        }
    }

    public function index()
    {
        $user = Auth::user();

        $pedido = Pedido::where('cliente_id', $user->cliente_id)
            ->where('estado_id', 1)
            ->with('detalles.producto', 'detalles.variante')
            ->first();


        return view('carrito.index', compact('pedido'));
    }

    public function actualizarCantidad(Request $request, $id)
    {
        $detalle = DetallePedido::findOrFail($id);

        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $detalle->cantidad = $request->cantidad;
        $detalle->subtotal = $detalle->precio_unit * $request->cantidad;
        $detalle->save();

        // Recalcular el total del pedido
        $pedido = $detalle->pedido;
        $pedido->total = $pedido->detalles()->sum('subtotal');
        $pedido->save();

        return redirect()->back()->with('success', 'Cantidad actualizada correctamente');
    }

    public function eliminar($id)
    {
        $detalle = DetallePedido::findOrFail($id);
        $pedido = $detalle->pedido;

        $detalle->delete();

        // Actualizar el total del pedido
        $pedido->total = $pedido->detalles()->sum('subtotal');
        $pedido->save();

        return redirect()->route('carrito')->with('success', 'Producto eliminado del carrito');
    }

    public function vaciar()
    {
        $user = Auth::user();

        $pedido = Pedido::where('cliente_id', $user->cliente_id)
            ->where('estado_id', 1)
            ->with('detalles')
            ->first();

        if ($pedido) {
            foreach ($pedido->detalles as $detalle) {
                $detalle->delete();
            }
            $pedido->total = 0;
            $pedido->save();
        }

        return redirect()->route('carrito')->with('success', 'Carrito vaciado correctamente');
    }
}
