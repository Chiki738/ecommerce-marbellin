<?php

namespace App\Http\Controllers;

use App\Mail\NotificarCambioProductoEmail;
use App\Models\CambioProducto;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CambioProductoController extends Controller
{
    public function solicitar(Request $request)
    {
        $data = $request->validate([
            'pedido_id' => ['required', 'integer', 'exists:pedidos,id'],
            'detalle_pedido_id' => ['required', 'integer', 'exists:detalle_pedido,id'],
            'variante_id' => ['required', 'integer', 'exists:variantes_producto,id'],
            'comentario' => ['required', 'string', 'max:500'],
        ]);

        $detalle = DetallePedido::whereKey($data['detalle_pedido_id'])
            ->where('pedido_id', $data['pedido_id'])
            ->where('variante_id', $data['variante_id'])
            ->whereHas('pedido', function ($query) {
                $query->where('cliente_id', Auth::user()->cliente_id)
                    ->where('estado_id', 4)
                    ->where('fecha', '>=', now()->subDays(2)->startOfDay());
            })
            ->first();

        if (!$detalle) {
            return response()->json([
                'message' => 'No puedes solicitar un cambio para este producto.',
            ], 403);
        }

        $yaSolicitado = CambioProducto::where('detalle_pedido_id', $detalle->id)
            ->whereIn('estado', ['Pendiente', 'Aprobado'])
            ->exists();

        if ($yaSolicitado) {
            return response()->json([
                'message' => 'Ya existe una solicitud activa para este producto.',
            ], 422);
        }

        CambioProducto::create([
            'pedido_id' => $data['pedido_id'],
            'detalle_pedido_id' => $data['detalle_pedido_id'],
            'variante_antigua_id' => $data['variante_id'],
            'comentario_cliente' => $data['comentario'],
        ]);

        return response()->json(['message' => 'Tu solicitud de cambio fue enviada.']);
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'estado' => ['nullable', 'in:Todas,Pendiente,Aprobado,Rechazado,Cambiado'],
        ]);

        $estado = $data['estado'] ?? 'Todas';

        $query = CambioProducto::query();

        if ($estado !== 'Todas') {
            $query->where('estado', $estado);
        }

        $cambios = $query->with(['pedido.cliente', 'detalle.producto', 'detalle.variante'])
            ->orderByDesc('created_at')
            ->paginate(5);

        $productos = Producto::select('codigo', 'nombre')->orderBy('nombre')->get();
        $conteos = collect(['Todas' => CambioProducto::count()]);

        foreach (['Pendiente', 'Aprobado', 'Rechazado', 'Cambiado'] as $estadoConteo) {
            $conteos->put($estadoConteo, CambioProducto::where('estado', $estadoConteo)->count());
        }

        return view('admin.devolucionesAdmin', compact('cambios', 'productos', 'conteos'))
            ->with('productosModal', $productos);
    }


    public function procesar(Request $request, $id)
    {
        $data = $request->validate([
            'estado' => ['required', 'in:Aprobado,Rechazado,Cambiado'],
            'comentario_admin' => ['nullable', 'string', 'max:500'],
            'producto_codigo' => ['nullable', 'required_if:estado,Cambiado', 'exists:productos,codigo'],
            'talla_nueva' => ['nullable', 'required_if:estado,Cambiado', 'string', 'max:10'],
            'color_nuevo' => ['nullable', 'required_if:estado,Cambiado', 'string', 'max:40'],
            'notificar' => ['nullable', 'boolean'],
        ]);

        try {
            $cambio = DB::transaction(function () use ($id, $data) {
                $cambio = CambioProducto::with(['pedido.cliente', 'detalle'])
                    ->lockForUpdate()
                    ->findOrFail($id);

                $cambio->estado = $data['estado'];
                $cambio->comentario_admin = $data['comentario_admin'] ?? null;

                if ($data['estado'] === 'Cambiado') {
                    $variante = VarianteProducto::where('producto_codigo', $data['producto_codigo'])
                        ->where('talla', $data['talla_nueva'])
                        ->where('color', $data['color_nuevo'])
                        ->lockForUpdate()
                        ->first();

                    if (!$variante || $variante->cantidad < 1) {
                        throw ValidationException::withMessages([
                            'variante' => 'La variante seleccionada no existe o no tiene stock.',
                        ]);
                    }

                    $varianteAntigua = VarianteProducto::whereKey($cambio->detalle->variante_id)
                        ->lockForUpdate()
                        ->first();

                    $varianteAntigua?->increment('cantidad');
                    $variante->decrement('cantidad');
                    $cambio->variante_nueva_id = $variante->id;
                }

                $cambio->save();

                return $cambio->load(['pedido.cliente', 'detalle']);
            });
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => collect($exception->errors())->flatten()->first(),
            ], 422);
        }

        if (
            ($data['notificar'] ?? false) &&
            $cambio->pedido?->cliente?->email &&
            in_array($data['estado'], ['Aprobado', 'Rechazado'], true)
        ) {
            $mensaje = '';

            if ($data['estado'] === 'Aprobado') {
                $mensaje = "Tu solicitud de cambio #{$cambio->id} fue aprobada. Acércate al local para continuar el proceso.";
            } elseif ($data['estado'] === 'Rechazado') {
                $mensaje = "Tu solicitud de cambio #{$cambio->id} fue rechazada.";
                if (!empty($data['comentario_admin'])) {
                    $mensaje .= " Motivo: {$data['comentario_admin']}.";
                }
            }

            Mail::to($cambio->pedido->cliente->email)->send(
                new NotificarCambioProductoEmail($cambio, $mensaje)
            );
        }

        return response()->json(['message' => 'Solicitud procesada correctamente.']);
    }
}
