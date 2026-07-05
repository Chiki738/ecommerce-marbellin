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

    public function buscar(Request $request)
    {
        $request->validate([
            'producto_codigo' => ['required', 'string', 'exists:productos,codigo'],
            'talla' => ['required', 'string', 'max:10'],
            'color' => ['required', 'string', 'max:40'],
        ]);

        $variante = VarianteProducto::where('producto_codigo', $request->producto_codigo)
            ->where('talla', $request->talla)
            ->where('color', $request->color)
            ->first();

        if (!$variante) {
            return response()->json(['error' => 'Variante no encontrada'], 404);
        }

        return response()->json(['id' => $variante->id]);
    }
}
