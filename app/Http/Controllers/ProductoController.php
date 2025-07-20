<?php

namespace App\Http\Controllers;

use App\Models\{Producto, VarianteProducto, Categoria};
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    /** Vista admin de productos */
    public function index()
    {
        return view('admin.productosAdmin', [
            'productos' => Producto::with('categoria')->orderBy('nombre')->get(),
            'variantes' => VarianteProducto::all(),
            'categorias' => Categoria::all(),
        ]);
    }

    /** Guardar nuevo producto */
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
        $this->generarVariantes($producto->codigo);

        return redirect()->route('admin.productosAdmin')->with('success', 'Producto y variantes generadas automáticamente');
    }

    /** Eliminar producto por código */
    public function destroy($codigo)
    {
        Producto::where('codigo', $codigo)->firstOrFail()->delete();
        return back()->with('success', 'Producto eliminado correctamente.');
    }

    /** Actualizar producto */
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
        return response()->json(['message' => 'Producto actualizado correctamente']);
    }

    /** Mostrar productos al público */
    public function mostrarProductosPublico(Request $request)
    {
        $productos = Producto::with('categoria')
            ->when($request->buscar, fn($q) => $q->where(function ($q2) use ($request) {
                foreach (explode(' ', $request->buscar) as $palabra) {
                    $q2->orWhere('nombre', 'LIKE', "%$palabra%")
                        ->orWhere('descripcion', 'LIKE', "%$palabra%");
                }
            }))
            ->paginate(6);

        return view('producto.productos', [
            'productos' => $productos,
            'categorias' => Categoria::all(),
            'colores' => VarianteProducto::distinct()->pluck('color'),
            'tallas' => VarianteProducto::distinct()->pluck('talla'),
        ]);
    }

    /** Filtros por talla, color y categoría */
    public function filtrar(Request $request)
    {
        $filtros = $request->only(['colores', 'tallas', 'categorias']);

        $productos = Producto::with(['variantes' => function ($q) use ($filtros) {
            $q->where('cantidad', '>', 0)
                ->when($filtros['colores'] ?? [], fn($q) => $q->whereIn('color', $filtros['colores']))
                ->when($filtros['tallas'] ?? [], fn($q) => $q->whereIn('talla', $filtros['tallas']));
        }])
            ->whereHas('variantes', function ($q) use ($filtros) {
                $q->where('cantidad', '>', 0)
                    ->when($filtros['colores'] ?? [], fn($q) => $q->whereIn('color', $filtros['colores']))
                    ->when($filtros['tallas'] ?? [], fn($q) => $q->whereIn('talla', $filtros['tallas']));
            })
            ->when($filtros['categorias'] ?? [], fn($q) => $q->whereIn('categoria_id', $filtros['categorias']))
            ->paginate(6);

        return view('producto.productos', [
            'productos' => $productos,
            'categorias' => Categoria::all()
        ]);
    }

    /** Detalle de un producto */
    public function detalleProducto($codigo)
    {
        $producto = Producto::with(['variantes', 'categoria'])->where('codigo', $codigo)->firstOrFail();

        return view('producto.detalle', [
            'producto' => $producto,
            'categorias' => Categoria::all()
        ]);
    }

    /** Autocompletado inteligente */
    public function autocomplete(Request $request)
    {
        $query = $this->normalizar($request->input('query'));
        $terminos = explode(' ', $query);

        $productos = Producto::all()->map(function ($producto) use ($query, $terminos) {
            $nombre = $this->normalizar($producto->nombre);

            $puntaje = match (true) {
                $nombre === $query => 3,
                str_contains($nombre, $query) => 2,
                collect($terminos)->every(fn($t) => str_contains($nombre, $t)) => 1,
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

    // =========================
    // Funciones auxiliares
    // =========================

    private function normalizar($cadena)
    {
        return strtolower(Str::of(iconv('UTF-8', 'ASCII//TRANSLIT', $cadena))->replaceMatches('/[^\pL\d]+/u', ' '));
    }

    private function subirImagenCloudinary(Request $request): string
    {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
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

    private function generarVariantes(string $codigo): void
    {
        $tallas = ['S', 'M', 'L', 'XL'];
        $colores = ['Negro', 'Blanco', 'Rojo', 'Amarillo'];

        foreach ($tallas as $talla) {
            foreach ($colores as $color) {
                VarianteProducto::create([
                    'producto_codigo' => $codigo,
                    'talla' => $talla,
                    'color' => $color,
                    'cantidad' => 17,
                ]);
            }
        }
    }
}
