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

            // Obtener producto
            $producto = Producto::where('codigo', $request->producto_codigo)->firstOrFail();

            // Verificar si existe la variante y tiene suficiente stock
            $variante = \App\Models\VarianteProducto::where([
                ['producto_codigo', '=', $request->producto_codigo],
                ['talla', '=', $request->talla],
                ['color', '=', $request->color],
            ])->first();


            if (!$variante || $variante->cantidad < $request->cantidad) {
                return redirect()->back()->with('error', 'La combinación de talla y color no está disponible o no hay suficiente stock.');
            }


            // Obtener distrito y provincia para envío
            $distrito = $user->distrito;
            $provincia = $distrito?->provincia;

            // Obtener o crear pedido pendiente para el cliente
            $pedido = Pedido::firstOrCreate(
                ['cliente_id' => $user->cliente_id, 'estado_id' => 1], // ✅ 1 = pendiente
                [
                    'fecha' => now(),
                    'total' => 0,
                    'direccion_envio' => $user->direccion,
                    'distrito_id' => $user->distrito_id,
                ]
            );


            // Calcular subtotal
            $precioUnit = $producto->precio;
            $subtotal = $precioUnit * $request->cantidad;

            // Crear detalle de pedido (producto en carrito)
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_codigo' => $producto->codigo,
                'cantidad' => $request->cantidad,
                'precio_unit' => $precioUnit,
                'subtotal' => $subtotal,
                'variante_id' => $variante->id,
            ]);




            // Actualizar total del pedido
            $pedido->total += $subtotal;
            $pedido->save();

            Log::info("Producto agregado al pedido ID: " . $pedido->id);

            return redirect()->back()->with('success', 'Producto agregado al carrito');
        } catch (\Exception $e) {
            Log::error("Error al agregar al carrito: " . $e->getMessage());
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
}
