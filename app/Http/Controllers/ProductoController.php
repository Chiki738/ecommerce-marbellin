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

    public function destroy($codigo)
    {
        $producto = Producto::where('codigo', $codigo)->firstOrFail();
        $producto->delete();
        return redirect()->back()->with('success', 'Producto eliminado correctamente.');
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

        return redirect()->route('admin.productosAdmin')->with('success', 'Producto actualizado correctamente');
    }

    public function mostrarProductosPublico()
    {
        $productos = Producto::with('variantes')->get();
        return view('productos', compact('productos'));
    }

    public function filtrar(Request $request)
    {
        $colores = $request->input('colores', []);
        $tallas = $request->input('tallas', []);
        $categorias = $request->input('categorias', []);

        $productos = Producto::query();

        // Filtrar productos que tengan variantes con cantidad > 0 y coincidan con filtros de color y talla
        $productos->whereHas('variantes', function ($query) use ($colores, $tallas) {
            $query->where('cantidad', '>', 0);

            if (!empty($colores)) {
                $query->whereIn('color', $colores);
            }

            if (!empty($tallas)) {
                $query->whereIn('talla', $tallas);
            }
        });

        // Filtrar productos por categorías si se especifican
        if (!empty($categorias)) {
            $productos->whereIn('categoria', $categorias);
        }

        // Cargar variantes que cumplen los filtros, para mostrar solo las variantes válidas
        $productos = $productos->with(['variantes' => function ($query) use ($colores, $tallas) {
            $query->where('cantidad', '>', 0);

            if (!empty($colores)) {
                $query->whereIn('color', $colores);
            }

            if (!empty($tallas)) {
                $query->whereIn('talla', $tallas);
            }
        }])->get();

        return view('productos', compact('productos'));
    }

    public function detalleProducto($codigo)
    {
        // Cargar producto con variantes (solo disponibles)
        $producto = Producto::where('codigo', $codigo)->with('variantes')->firstOrFail();

        return view('producto.detalle', compact('producto'));
    }
}
