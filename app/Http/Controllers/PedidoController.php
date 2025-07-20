<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstadoPedidoActualizado;
use App\Models\User;
use App\Models\{Pedido, DetallePedido, Producto, VarianteProducto};
use App\Models\EstadoPedido;
use Barryvdh\DomPDF\Facade\Pdf;


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

            return $this->respuesta($request, 'Producto agregado al carrito');
        } catch (\Exception $e) {
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

        $pedido = $detalle->pedido;
        $pedido->update(['total' => $pedido->detalles()->sum('subtotal')]);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada',
            'subtotal' => $detalle->subtotal,
            'total' => $pedido->total,
            'producto' => $detalle->producto->nombre,
            'resumenCantidad' => $pedido->detalles
                ->where('producto_codigo', $detalle->producto_codigo)
                ->sum('cantidad'),
            'resumenSubtotal' => $pedido->detalles
                ->where('producto_codigo', $detalle->producto_codigo)
                ->sum('subtotal'),
        ]);
    }

    public function eliminar(Request $request, $id)
    {
        $detalle = DetallePedido::findOrFail($id);
        $pedido = $detalle->pedido;

        $detalle->delete();
        $pedido->update(['total' => $pedido->detalles()->sum('subtotal')]);

        return response()->json([
            'message' => 'Producto eliminado del carrito',
            'total' => $pedido->total,
        ]);
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

        return response()->json([
            'message' => 'Carrito vaciado correctamente',
            'total' => 0,
        ]);
    }

    /**
     * Retorna respuesta JSON o redirect según tipo de request.
     */
    private function respuesta(Request $request, string $mensaje, int $code = 200, bool $esError = false)
    {
        return response()->json([
            $esError ? 'error' : 'message' => $mensaje
        ], $code);
    }
    public function index()
    {
        $pedidos = Pedido::with('cliente')->get();

        return view('admin.pedidosAdmin', compact('pedidos'));
    }

    public function buscarPorFiltros(Request $request)
    {
        $id = $request->query('id');
        $email = $request->query('email');
        $estado = $request->query('estado');
        $fecha = $request->query('fecha');
        $perPage = $request->query('perPage', 10);

        // Si se proporciona ID del pedido, devolver solo ese
        if ($id) {
            $pedido = Pedido::with(['cliente', 'detalles'])->find($id);
            if (!$pedido) {
                return response('', 204);
            }

            return view('partials.pedidosCliente', [
                'pedidos' => collect([$pedido]), // para que funcione igual que una colección paginada
                'cliente' => $pedido->cliente
            ])->render();
        }

        $query = Pedido::with(['cliente', 'detalles']);

        if ($email) {
            $cliente = User::where('email', $email)->first();
            if (!$cliente) {
                return response('', 204);
            }
            $query->where('cliente_id', $cliente->cliente_id);
        }

        if ($estado) {
            $query->where('estado_id', $estado);
        }

        if ($fecha === 'hoy') {
            $query->whereDate('fecha', now()->toDateString());
        }

        if ($fecha === 'semana') {
            $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        if ($fecha === 'mes') {
            $query->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year);
        }

        $pedidos = $query->paginate($perPage);

        if ($pedidos->isEmpty()) {
            return response('', 204);
        }

        return view('partials.pedidosCliente', [
            'pedidos' => $pedidos,
            'cliente' => $email ? $cliente ?? null : null
        ])->render();
    }

    public function cambiarEstado(Request $request, $id)
    {
        $pedido = Pedido::with('cliente')->findOrFail($id);
        $estado = EstadoPedido::findOrFail($request->estado_id);
        $pedido->estado_id = $estado->id;
        $pedido->save();

        // Enviar email al cliente
        if ($pedido->cliente && $pedido->cliente->email) {
            Mail::to($pedido->cliente->email)->send(new EstadoPedidoActualizado($pedido, $estado->nombre));
        }

        return response()->json([
            'success' => true,
            'estado' => ucfirst($estado->nombre),
            'clase' => match ($estado->nombre) {
                'pendiente' => 'bg-warning',
                'procesando' => 'bg-primary',
                'enviado' => 'bg-info',
                'entregado' => 'bg-success',
                'cancelado' => 'bg-danger',
                default => 'bg-secondary',
            },
            'message' => 'Estado actualizado correctamente.'
        ]);
    }

    public function cancelar($id)
    {
        $pedido = Pedido::with('cliente')->findOrFail($id);
        $estado = EstadoPedido::where('nombre', 'cancelado')->first();
        $pedido->estado_id = $estado->id;
        $pedido->save();

        // Enviar email al cliente
        if ($pedido->cliente && $pedido->cliente->email) {
            Mail::to($pedido->cliente->email)->send(new EstadoPedidoActualizado($pedido, 'cancelado'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Pedido cancelado correctamente.'
        ]);
    }

    public function detalle($id)
    {
        $pedido = Pedido::with([
            'cliente.distrito',
            'cliente.distrito.provincia', // 👈 importante
            'estado',
            'detalles.producto',
            'detalles.variante'
        ])->findOrFail($id);


        $estados = EstadoPedido::all(); // ✅ Necesario para el modal

        return view('admin.detallePedidoAdmin', compact('pedido', 'estados'));
    }


    public function imprimir($id)
    {
        $pedido = Pedido::with(['cliente.distrito.provincia', 'detalles.producto', 'detalles.variante'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.pedidos.pdf', compact('pedido'));
        return $pdf->download('pedido_' . $pedido->id . '.pdf');
    }

    public function historial()
    {
        $pedidos = Pedido::with(['detalles.producto', 'detalles.variante', 'estado'])
            ->where('cliente_id', Auth::user()->cliente_id)
            ->latest()
            ->paginate(4); // 👈 Paginación de 4 pedidos por página

        return view('client.historialPedidos', compact('pedidos'));
    }
}
