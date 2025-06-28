<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">Filtrar productos</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('productos.filtrar') }}">
            {{-- Categorías --}}
            <div class="mb-3">
                <strong class="form-label">Categorías</strong>
                @php $categorias = \App\Models\Categoria::pluck('nombre', 'categoria_id'); @endphp
                @foreach($categorias as $id => $nombre)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="categorias[]" value="{{ $id }}" id="cat{{ $id }}" {{ in_array($id, request('categorias', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="cat{{ $id }}">{{ $nombre }}</label>
                </div>
                @endforeach
            </div>

            {{-- Colores --}}
            <div class="mb-3">
                <strong class="form-label">Colores</strong>
                @php $colores = ['Blanco', 'Negro', 'Rojo', 'Amarillo']; @endphp
                @foreach($colores as $color)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="colores[]" value="{{ $color }}" id="color{{ $color }}" {{ in_array($color, request('colores', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="color{{ $color }}">{{ $color }}</label>
                </div>
                @endforeach
            </div>

            {{-- Tallas --}}
            <div class="mb-3">
                <strong class="form-label">Tallas</strong>
                @php $tallas = ['S', 'M', 'L', 'XL']; @endphp
                @foreach($tallas as $talla)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tallas[]" value="{{ $talla }}" id="talla{{ $talla }}" {{ in_array($talla, request('tallas', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="talla{{ $talla }}">{{ $talla }}</label>
                </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-outline-primary w-100">Aplicar filtros</button>
        </form>
    </div>
</div>