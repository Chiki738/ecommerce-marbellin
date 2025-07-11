<!-- Modal Agregar Producto -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="formAgregarProducto" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    {{-- Validación --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Inputs de texto --}}
                    @foreach ([
                    ['name' => 'codigo', 'type' => 'text', 'label' => 'Código'],
                    ['name' => 'nombre', 'type' => 'text', 'label' => 'Nombre'],
                    ['name' => 'precio', 'type' => 'number', 'label' => 'Precio', 'step' => '0.01'],
                    ] as $input)
                    <div class="mb-3">
                        <label for="{{ $input['name'] }}" class="form-label">{{ $input['label'] }}</label>
                        <input
                            type="{{ $input['type'] }}"
                            name="{{ $input['name'] }}"
                            id="{{ $input['name'] }}"
                            class="form-control"
                            placeholder="{{ $input['label'] }}"
                            {{ isset($input['step']) ? "step={$input['step']}" : '' }}
                            required>
                    </div>
                    @endforeach

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" placeholder="Descripción del producto" required></textarea>
                    </div>

                    {{-- Imagen --}}
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen</label>
                        <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*" required>
                    </div>

                    {{-- Categoría --}}
                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoría</label>
                        <select name="categoria_id" id="categoria_id" class="form-select" required>
                            <option value="" disabled selected>Selecciona una categoría</option>
                            @foreach($categorias as $categoria)
                            <option value="{{ $categoria->categoria_id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle me-1"></i> Guardar Producto
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/productosAdmin/agregarProducto.js') }}"></script>
@endpush