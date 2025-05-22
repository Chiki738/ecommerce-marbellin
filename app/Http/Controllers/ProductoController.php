<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\VarianteProducto;


class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        $variantes = VarianteProducto::all();

        return view('admin.productos', compact('productos', 'variantes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:productos,codigo',
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'descripcion' => 'required|string',
            'imagen' => 'required|image|max:10240',
            'categoria' => 'required|string|max:255',
        ]);

        $rutaImagen = $request->file('imagen')->store('productos', 'public');

        $producto = Producto::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'descripcion' => $request->descripcion,
            'imagen' => $rutaImagen,
            'categoria' => $request->categoria,
        ]);

        // Tallas y colores predeterminados
        $tallas = ['S', 'M', 'L'];
        $colores = ['Negro', 'Blanco'];
        $cantidadPorDefecto = 10;

        foreach ($tallas as $talla) {
            foreach ($colores as $color) {
                VarianteProducto::create([
                    'producto_codigo' => $producto->codigo,
                    'talla' => $talla,
                    'color' => $color,
                    'cantidad' => $cantidadPorDefecto,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Producto y variantes generadas automáticamente');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return redirect()->back()->with('success', 'Producto y sus variantes eliminados correctamente.');
    }

    public function update(Request $request, $codigo)
    {
        $producto = Producto::where('codigo', $codigo)->firstOrFail();

        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio;
        $producto->categoria = $request->categoria;
        $producto->descripcion = $request->descripcion;

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagen;
        }

        $producto->save();

        return redirect()->route('admin.productos')->with('success', 'Producto actualizado correctamente');
    }
}
