<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VarianteProducto;

class VarianteController extends Controller
{
    public function actualizarCantidad(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:0',
        ]);

        $variante = VarianteProducto::findOrFail($id);
        $variante->cantidad = $request->cantidad;
        $variante->save();

        // ✅ Esto permite a JavaScript continuar y actualizar colores correctamente
        return response()->json(['message' => 'Cantidad actualizada']);
    }
}
