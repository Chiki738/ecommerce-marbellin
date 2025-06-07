<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Distrito;

class PedidoController extends Controller
{
    public function agregarAlCarrito(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

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

            // Obtener distrito y provincia para la dirección del envío
            $distrito = Distrito::find($user->distrito_id);
            $provincia = $distrito ? $distrito->provincia : null;

            $pedido = Pedido::firstOrCreate(
                ['cliente_id' => $user->cliente_id, 'estado' => 'pendiente'],
                [
                    'fecha' => now(),
                    'total' => 0,
                    'direccion_envio' => $user->direccion,
                    'distrito' => $distrito ? $distrito->nombre : null,
                    'provincia' => $provincia ? $provincia->nombre : null,
                ]
            );

            $precioUnit = $producto->precio;
            $subtotal = $precioUnit * $request->cantidad;

            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_codigo' => $producto->codigo,
                'talla' => $request->talla,
                'color' => $request->color,
                'cantidad' => $request->cantidad,
                'precio_unit' => $precioUnit,
                'subtotal' => $subtotal,
            ]);

            $pedido->total += $subtotal;
            $pedido->save();

            Log::info("Producto agregado al pedido ID: " . $pedido->id);

            return redirect()->back()->with('success', 'Producto agregado al carrito');
        } catch (\Exception $e) {
            Log::error("Error al agregar al carrito: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al agregar el producto');
        }
    }
}
