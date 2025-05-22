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

        return redirect()->back()->with('success', 'Cantidad actualizada correctamente.');
    }
}
