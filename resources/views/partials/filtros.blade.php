@php
$filtros = [
['label' => 'Categorías', 'name' => 'categorias', 'items' => \App\Models\Categoria::pluck('nombre', 'categoria_id')],
['label' => 'Colores', 'name' => 'colores', 'items' => collect(['Blanco', 'Negro', 'Rojo', 'Amarillo'])->mapWithKeys(fn($c) => [$c => $c])],
['label' => 'Tallas', 'name' => 'tallas', 'items' => collect(['S', 'M', 'L', 'XL'])->mapWithKeys(fn($t) => [$t => $t])],
];
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">Filtrar productos</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('productos.filtrar') }}">
            @foreach ($filtros as $filtro)
            <div class="mb-3">
                <strong class="form-label">{{ $filtro['label'] }}</strong>
                @foreach ($filtro['items'] as $valor => $texto)
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="{{ $filtro['name'] }}[]"
                        value="{{ $valor }}"
                        id="{{ $filtro['name'] . $valor }}"
                        {{ in_array($valor, request($filtro['name'], [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $filtro['name'] . $valor }}">{{ $texto }}</label>
                </div>
                @endforeach
            </div>
            @endforeach

            <button type="submit" class="btn btn-outline-primary w-100">Aplicar filtros</button>
        </form>
    </div>
</div>