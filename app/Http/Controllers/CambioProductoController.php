<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CambioProducto;
use App\Models\Producto; // Asegúrate de importar el modelo
use Illuminate\Support\Facades\Mail;

class CambioProductoController extends Controller
{
    // Cliente solicita cambio
    public function solicitar(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'detalle_pedido_id' => 'required|exists:detalle_pedido,id',
            'variante_id' => 'required|exists:variantes_producto,id',
            'comentario' => 'required|string|max:500'
        ]);

        CambioProducto::create([
            'pedido_id' => $request->pedido_id,
            'detalle_pedido_id' => $request->detalle_pedido_id,
            'variante_antigua_id' => $request->variante_id,
            'comentario_cliente' => $request->comentario
        ]);

        return response()->json(['message' => 'Tu solicitud de cambio fue enviada.']);
    }
    public function index()
    {
        $estado = request('estado', 'Todas');

        $query = \App\Models\CambioProducto::query();

        if ($estado !== 'Todas') {
            $query->where('estado', $estado);
        }

        $cambios = $query->with(['pedido.cliente', 'detalle.producto', 'detalle.variante'])
            ->orderByDesc('created_at')
            ->paginate(5);

        // Solo una vez obtenemos los productos
        $productos = Producto::select('codigo', 'nombre')->orderBy('nombre')->get();

        // Reutilizamos la misma variable para dos nombres
        return view('admin.devolucionesAdmin', compact('cambios'))->with([
            'productos' => $productos,
            'productosModal' => $productos
        ]);
    }


    public function procesar(Request $request, $id)
    {
        $cambio = CambioProducto::with(['pedido.cliente', 'detalle'])->findOrFail($id);

        $estado = $request->estado;
        $comentarioAdmin = $request->comentario_admin;
        $varianteNuevaId = $request->variante_nueva_id;
        $notificar = $request->notificar;

        $cambio->estado = $estado;
        $cambio->comentario_admin = $comentarioAdmin;

        // Solo si es cambio de producto (no se envía correo)
        if ($estado === 'Cambiado' && $varianteNuevaId) {
            $partes = explode('-', $varianteNuevaId); // ej. "5-M-Negro"
            if (count($partes) === 3) {
                [$productoId, $talla, $color] = $partes;

                $variante = \App\Models\VarianteProducto::where('producto_codigo', $productoId)
                    ->where('talla', $talla)
                    ->where('color', $color)
                    ->first();

                if (!$variante) {
                    return response()->json(['message' => 'La variante seleccionada no existe.'], 422);
                }

                // Actualizar variante nueva
                $cambio->variante_nueva_id = $variante->id;

                // Ajustar stock: disminuir antigua, aumentar nueva
                $varianteAntigua = \App\Models\VarianteProducto::find($cambio->detalle->variante_id);
                if ($varianteAntigua) {
                    $varianteAntigua->cantidad += 1;
                    $varianteAntigua->save();
                }

                $variante->cantidad -= 1;
                $variante->save();
            } else {
                return response()->json(['message' => 'Formato de variante inválido.'], 422);
            }
        }

        $cambio->save();

        // Enviar notificación solo si es Aprobado o Rechazado
        if (
            $notificar &&
            $cambio->pedido?->cliente?->email &&
            in_array($estado, ['Aprobado', 'Rechazado'])
        ) {
            // Mensaje personalizado
            $mensaje = '';

            if ($estado === 'Aprobado') {
                $mensaje = "✅ Tu solicitud de cambio del pedido #{$cambio->id} fue *aprobada*. Acerte al local para su respectivo proceso.";
            } elseif ($estado === 'Rechazado') {
                $mensaje = "❌ Tu solicitud de cambio del pedido #{$cambio->id} fue *rechazada*.";
                if (!empty($comentarioAdmin)) {
                    $mensaje .= " Motivo: {$comentarioAdmin}.";
                }
            }

            Mail::to($cambio->pedido->cliente->email)->send(
                new \App\Mail\NotificarCambioProductoEmail($cambio, $mensaje)
            );
        }

        return response()->json(['message' => 'Solicitud procesada correctamente.']);
    }
}
