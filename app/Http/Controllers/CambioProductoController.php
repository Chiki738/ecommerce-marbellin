<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CambioProducto;
use App\Notifications\NotificarCambioProducto;
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

    // Admin ve todos los cambios
    public function index()
    {
        $cambios = CambioProducto::with(['pedido.cliente', 'detalle.producto', 'detalle.variante'])->get();
        $productos = Producto::all(); // Obtener todos los productos

        return view('admin.devolucionesAdmin', compact('cambios', 'productos'));
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

        if ($estado === 'Aprobado' && $varianteNuevaId) {
            $cambio->variante_nueva_id = $varianteNuevaId;
        }

        $cambio->save();

        // Enviar notificación si está activado
        if ($notificar && $cambio->pedido?->cliente?->email) {
            $mensaje = match ($estado) {
                'Aprobado' => 'Tu solicitud ha sido aprobada y el cambio se realizará pronto.',
                'Rechazado' => 'Tu solicitud fue rechazada. Comentario del administrador: ' . $comentarioAdmin,
                default => 'Tu solicitud ha sido actualizada.',
            };

            Mail::to($cambio->pedido->cliente->email)->send(new \App\Mail\NotificarCambioProductoEmail($cambio, $mensaje));
        }

        return response()->json(['message' => 'Solicitud procesada correctamente.']);
    }
}
