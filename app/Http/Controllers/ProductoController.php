<?php

namespace App\Http\Controllers;

use App\Models\{Producto, VarianteProducto, Categoria};
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

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
            'codigo' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:productos,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0.01'],
            'descripcion' => ['required', 'string', 'max:2000'],
            'imagen' => ['required', 'image', 'max:5120'],
            'categoria_id' => ['required', 'exists:categorias,categoria_id'],
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
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0.01'],
            'descripcion' => ['required', 'string', 'max:2000'],
            'categoria_id' => ['required', 'exists:categorias,categoria_id'],
            'imagen' => ['nullable', 'image', 'max:5120'],
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
        $data = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);

        $buscar = trim($data['buscar'] ?? '');

        $productos = Producto::with('categoria')
            ->when($buscar !== '', fn($q) => $q->where(function ($q2) use ($buscar) {
                foreach (array_filter(explode(' ', $buscar)) as $palabra) {
                    $q2->orWhere('nombre', 'LIKE', "%$palabra%")
                        ->orWhere('descripcion', 'LIKE', "%$palabra%");
                }
            }))
            ->orderBy('nombre')
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
        $data = $request->validate([
            'colores' => ['nullable', 'array'],
            'colores.*' => ['string', 'max:40'],
            'tallas' => ['nullable', 'array'],
            'tallas.*' => ['string', 'max:10'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['integer', 'exists:categorias,categoria_id'],
        ]);

        $filtros = [
            'colores' => $data['colores'] ?? [],
            'tallas' => $data['tallas'] ?? [],
            'categorias' => $data['categorias'] ?? [],
        ];

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
            ->when($filtros['categorias'], fn($q) => $q->whereIn('categoria_id', $filtros['categorias']))
            ->orderBy('nombre')
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
        $data = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:60'],
        ]);

        $query = $this->normalizar($data['query']);
        $terminos = explode(' ', $query);

        $productos = Producto::select(['codigo', 'nombre', 'precio', 'imagen'])
            ->orderBy('nombre')
            ->limit(100)
            ->get()
            ->map(function ($producto) use ($query, $terminos) {
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

    private function normalizar(string $cadena): string
    {
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena) ?: $cadena;

        return Str::of($texto)
            ->lower()
            ->replaceMatches('/[^\pL\d]+/u', ' ')
            ->squish()
            ->toString();
    }

    private function subirImagenCloudinary(Request $request): string
    {
        $config = config('services.cloudinary');

        if (blank($config['cloud_name']) || blank($config['api_key']) || blank($config['api_secret'])) {
            throw new RuntimeException('Cloudinary no está configurado.');
        }

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $config['cloud_name'],
                'api_key' => $config['api_key'],
                'api_secret' => $config['api_secret'],
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
