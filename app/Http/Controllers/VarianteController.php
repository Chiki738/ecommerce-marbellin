<?php

namespace App\Http\Controllers;

use App\Models\VarianteProducto;
use Illuminate\Http\Request;

class VarianteController extends Controller
{
    public function actualizarCantidad(Request $request, VarianteProducto $variante)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:0',
        ]);

        $variante->update($validated);

        return response()->json(['message' => 'Cantidad actualizada']);
    }
}
