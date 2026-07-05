<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            return $this->respuesta('Debes iniciar sesión', 401, true);
        }

        $data = $request->validate([
            'producto_codigo' => ['required', 'string', 'exists:productos,codigo'],
            'talla' => ['required', 'string', 'max:10'],
            'color' => ['required', 'string', 'max:40'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $user = Auth::user();
            $producto = Producto::where('codigo', $data['producto_codigo'])->firstOrFail();

            $variante = VarianteProducto::where([
                ['producto_codigo', $data['producto_codigo']],
                ['talla', $data['talla']],
                ['color', $data['color']],
            ])->first();

            if (!$variante || $variante->cantidad < $data['cantidad']) {
                return $this->respuesta('Talla/color no disponible o stock insuficiente', 422, true);
            }

            DB::transaction(function () use ($user, $producto, $variante, $data) {
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

                $cantidad = (int) ($detalle->cantidad ?? 0) + (int) $data['cantidad'];

                if ($cantidad > $variante->cantidad) {
                    throw new \RuntimeException('La cantidad solicitada supera el stock disponible.', 422);
                }

                $detalle->cantidad = $cantidad;
                $detalle->precio_unit = $producto->precio;
                $detalle->subtotal = $producto->precio * $cantidad;
                $detalle->save();

                $pedido->update(['total' => $pedido->detalles()->sum('subtotal')]);
            });

            return $this->respuesta('Producto agregado al carrito');
        } catch (\Exception $e) {
            $status = (int) ($e->getCode() ?: 500);
            $status = $status >= 400 && $status < 600 ? $status : 500;

            return $this->respuesta($e->getMessage() ?: 'Ocurrió un error al agregar el producto', $status, true);
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
        $data = $request->validate(['cantidad' => ['required', 'integer', 'min:1']]);

        $detalle = $this->detalleCarritoDelUsuario($id);

        if (!$detalle->variante || $data['cantidad'] > $detalle->variante->cantidad) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente para la cantidad solicitada.',
            ], 422);
        }

        $detalle->update([
            'cantidad' => $data['cantidad'],
            'subtotal' => $detalle->precio_unit * $data['cantidad'],
        ]);

        $pedido = $detalle->pedido()->with('detalles')->firstOrFail();
        $pedido->update(['total' => $pedido->detalles()->sum('subtotal')]);
        $pedido->load('detalles');

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
        $detalle = $this->detalleCarritoDelUsuario($id);
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
    private function respuesta(string $mensaje, int $code = 200, bool $esError = false)
    {
        return response()->json([
            $esError ? 'error' : 'message' => $mensaje
        ], $code);
    }

    private function detalleCarritoDelUsuario($id): DetallePedido
    {
        return DetallePedido::with(['pedido', 'producto', 'variante'])
            ->whereKey($id)
            ->whereHas('pedido', function ($query) {
                $query->where('cliente_id', Auth::user()->cliente_id)
                    ->where('estado_id', 1);
            })
            ->firstOrFail();
    }

    public function index()
    {
        $pedidos = Pedido::with('cliente')->get();

        return view('admin.pedidosAdmin', compact('pedidos'));
    }

    public function buscarPorFiltros(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'email' => ['nullable', 'email'],
            'estado' => ['nullable', 'integer', 'exists:estado_pedido,id'],
            'fecha' => ['nullable', 'in:hoy,semana,mes'],
            'perPage' => ['nullable', 'integer', 'in:5,10,20'],
        ]);

        $id = $data['id'] ?? null;
        $email = $data['email'] ?? null;
        $estado = $data['estado'] ?? null;
        $fecha = $data['fecha'] ?? null;
        $perPage = $data['perPage'] ?? 10;

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
        $data = $request->validate([
            'estado_id' => ['required', 'integer', 'exists:estado_pedido,id'],
        ]);

        $pedido = Pedido::with('cliente')->findOrFail($id);
        $estado = EstadoPedido::findOrFail($data['estado_id']);
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
        $estado = EstadoPedido::where('nombre', 'cancelado')->firstOrFail();
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
