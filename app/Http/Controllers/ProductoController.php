<?php

namespace App\Http\Controllers;

use App\Models\{Producto, VarianteProducto, Categoria};
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return view('admin.productosAdmin', [
            'productos' => Producto::all(),
            'variantes' => VarianteProducto::all(),
            'categorias' => Categoria::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|unique:productos,codigo',
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'descripcion' => 'required|string',
            'imagen' => 'required|image|max:10240',
            'categoria_id' => 'required|exists:categorias,categoria_id',
        ]);

        $data['imagen'] = $this->subirImagenCloudinary($request);

        $producto = Producto::create($data);

        $tallas = ['S', 'M', 'L', 'XL'];
        $colores = ['Negro', 'Blanco', 'Rojo', 'Amarillo'];

        foreach ($tallas as $talla) {
            foreach ($colores as $color) {
                VarianteProducto::create([
                    'producto_codigo' => $producto->codigo,
                    'talla' => $talla,
                    'color' => $color,
                    'cantidad' => 17,
                ]);
            }
        }

        return back()->with('success', 'Producto y variantes generadas automáticamente');
    }

    public function destroy($codigo)
    {
        Producto::where('codigo', $codigo)->firstOrFail()->delete();
        return back()->with('success', 'Producto eliminado correctamente.');
    }

    public function update(Request $request, $codigo)
    {
        $producto = Producto::where('codigo', $codigo)->firstOrFail();

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categorias,categoria_id',
            'imagen' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->subirImagenCloudinary($request);
        }

        $producto->update($data);

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
                    $q->orWhere('nombre', 'LIKE', "%$palabra%")
                        ->orWhere('descripcion', 'LIKE', "%$palabra%");
                }
            });
        }

        return view('producto.productos', [
            'productos' => $query->with('categoria')->paginate(6),
            'categorias' => Categoria::all(),
            'colores' => VarianteProducto::select('color')->distinct()->pluck('color'),
            'tallas' => VarianteProducto::select('talla')->distinct()->pluck('talla'),
        ]);
    }

    public function filtrar(Request $request)
    {
        $colores = $request->input('colores', []);
        $tallas = $request->input('tallas', []);
        $categorias = $request->input('categorias', []);

        $productos = Producto::query()
            ->whereHas('variantes', function ($query) use ($colores, $tallas) {
                $query->where('cantidad', '>', 0)
                    ->when($colores, fn($q) => $q->whereIn('color', $colores))
                    ->when($tallas, fn($q) => $q->whereIn('talla', $tallas));
            })
            ->when($categorias, fn($q) => $q->whereIn('categoria_id', $categorias))
            ->with(['variantes' => function ($query) use ($colores, $tallas) {
                $query->where('cantidad', '>', 0)
                    ->when($colores, fn($q) => $q->whereIn('color', $colores))
                    ->when($tallas, fn($q) => $q->whereIn('talla', $tallas));
            }])
            ->paginate(6);

        return view('producto.productos', [
            'productos' => $productos,
            'categorias' => Categoria::all()
        ]);
    }

    public function detalleProducto($codigo)
    {
        $producto = Producto::with(['variantes', 'categoria'])->where('codigo', $codigo)->firstOrFail();
        return view('producto.detalle', [
            'producto' => $producto,
            'categorias' => Categoria::all()
        ]);
    }

    public function autocomplete(Request $request)
    {
        $query = $this->normalizar($request->input('query'));
        $terminos = explode(' ', $query);

        $productos = Producto::all()->map(function ($producto) use ($query, $terminos) {
            $nombre = $this->normalizar($producto->nombre);
            $puntaje = match (true) {
                $nombre === $query => 3,
                str_contains($nombre, $query) => 2,
                collect($terminos)->every(fn($p) => str_contains($nombre, $p)) => 1,
                default => 0,
            };

            return compact('producto', 'puntaje');
        });

        $max = $productos->max('puntaje');

        return response()->json(
            $productos->filter(fn($p) => $p['puntaje'] === $max && $max > 0)
                ->unique(fn($p) => $p['producto']->nombre)
                ->take(5)
                ->values()
                ->map(fn($p) => collect($p['producto'])->only(['codigo', 'nombre', 'precio', 'imagen']))
        );
    }

    private function normalizar($cadena)
    {
        return strtolower(preg_replace('~[^\pL\d]+~u', ' ', iconv('UTF-8', 'ASCII//TRANSLIT', $cadena)));
    }

    private function subirImagenCloudinary(Request $request): string
    {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true]
        ]);

        return $cloudinary->uploadApi()->upload(
            $request->file('imagen')->getRealPath(),
            [
                'folder' => 'samples/ecommerce',
                'use_filename' => true,
                'unique_filename' => false,
                'overwrite' => false,
            ]
        )['secure_url'];
    }
}
