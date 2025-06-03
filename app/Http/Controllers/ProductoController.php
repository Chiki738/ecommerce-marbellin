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

        return view('admin.productosAdmin', compact('productos', 'variantes'));
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
        $tallas = ['S', 'M', 'L', 'XL'];
        $colores = ['Negro', 'Blanco', 'Rojo', 'Amarillo'];
        $cantidadPorDefecto = 17;

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

    public function mostrarProductosPublico()
    {
        $productos = Producto::all();
        return view('home', compact('productos'));
    }

    public function filtrarPorCategoria($nombre)
    {
        // Convierte "Semi Hilos" en "semi_hilos"
        $claveCategoria = strtolower(str_replace(' ', '_', $nombre));

        // Filtra en la base de datos con ese valor
        $productos = Producto::where('categoria', $claveCategoria)->get();

        return view('home', compact('productos'))->with('categoriaSeleccionada', $nombre);
    }
}
