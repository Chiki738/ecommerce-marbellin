<?php

namespace App\Http\Controllers;

use App\Models\VarianteProducto;
use Illuminate\Http\Request;

class VarianteController extends Controller
{
    public function actualizarCantidad(Request $request, VarianteProducto $variante)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:0',
        ]);

        $variante->cantidad = $request->input('cantidad');
        $variante->save();

        return response()->json(['message' => 'Cantidad actualizada']);
    }
}
