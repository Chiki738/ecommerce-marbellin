<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\VarianteProducto;
use Cloudinary\Cloudinary;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        // Configura Cloudinary
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        // Subir imagen a Cloudinary
        $uploadedFileUrl = $cloudinary->uploadApi()->upload(
            $request->file('imagen')->getRealPath(),
            [
                'folder' => 'samples/ecommerce', // mismo folder que configuraste en Cloudinary
                'use_filename' => true,
                'unique_filename' => false,
                'overwrite' => false,
            ]
        );

        $urlImagen = $uploadedFileUrl['secure_url']; // URL pública de la imagen subida

        $producto = Producto::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'descripcion' => $request->descripcion,
            'imagen' => $urlImagen,  // guardamos la URL de Cloudinary
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
            // Instancia Cloudinary aquí también
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true
                ]
            ]);

            $uploadedFileUrl = $cloudinary->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                [
                    'folder' => 'samples/ecommerce',
                    'use_filename' => true,
                    'unique_filename' => false,
                    'overwrite' => false,
                ]
            );
            $producto->imagen = $uploadedFileUrl['secure_url'];
        }

        $producto->save();

        return redirect()->route('admin.productosAdmin')->with('success', 'Producto actualizado correctamente');
    }


    public function mostrarProductosPublico(Request $request)
    {
        $buscar = $request->input('buscar');
        $query = Producto::query();

        if ($buscar) {
            $terminos = explode(' ', $buscar);

            $query->where(function ($q) use ($terminos) {
                foreach ($terminos as $palabra) {
                    $q->orWhere('nombre', 'LIKE', '%' . $palabra . '%')
                        ->orWhere('descripcion', 'LIKE', '%' . $palabra . '%');
                }
            });
        }

        $productos = $query->get();

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

    public function autocomplete(Request $request)
    {
        $queryOriginal = $request->input('query');
        $query = $this->normalizar($queryOriginal);
        $terminos = explode(' ', $query);

        // Calcular puntaje
        $productos = Producto::all()->map(function ($producto) use ($query, $terminos) {
            $nombreNormalizado = $this->normalizar($producto->nombre);
            $puntaje = 0;

            if ($nombreNormalizado === $query) {
                $puntaje = 3;
            } elseif (str_contains($nombreNormalizado, $query)) {
                $puntaje = 2;
            } elseif (collect($terminos)->every(fn($p) => str_contains($nombreNormalizado, $p))) {
                $puntaje = 1;
            }

            return [
                'producto' => $producto,
                'puntaje' => $puntaje,
            ];
        });

        // Filtrar por el puntaje más alto
        $puntajeMaximo = $productos->max('puntaje');

        $filtrados = $productos
            ->filter(fn($item) => $item['puntaje'] === $puntajeMaximo && $item['puntaje'] > 0)
            ->unique(fn($item) => $item['producto']->nombre)
            ->take(5)
            ->values();

        return response()->json($filtrados->map(function ($item) {
            $producto = $item['producto'];
            return [
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'imagen' => $producto->imagen,
            ];
        }));
    }

    private function normalizar($cadena)
    {
        return strtolower(preg_replace(
            '~[^\pL\d]+~u',
            ' ',
            iconv('UTF-8', 'ASCII//TRANSLIT', $cadena)
        ));
    }
}
