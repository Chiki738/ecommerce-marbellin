<!-- Modal Agregar Producto -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="formAgregarProducto" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Agregar producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <!-- Errores de validación -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @foreach ([
                    ['name' => 'codigo', 'type' => 'text', 'placeholder' => 'Código'],
                    ['name' => 'nombre', 'type' => 'text', 'placeholder' => 'Nombre'],
                    ['name' => 'precio', 'type' => 'number', 'placeholder' => 'Precio', 'step' => '0.01'],
                    ] as $input)
                    <input
                        type="{{ $input['type'] }}"
                        name="{{ $input['name'] }}"
                        placeholder="{{ $input['placeholder'] }}"
                        class="form-control mb-2"
                        {{ isset($input['step']) ? "step={$input['step']}" : '' }}
                        required>
                    @endforeach

                    <textarea name="descripcion" placeholder="Descripción" class="form-control mb-2" required></textarea>

                    <input type="file" name="imagen" class="form-control mb-2" required accept="image/*">

                    <select name="categoria_id" id="categoria_id" class="form-control mb-2" required>
                        <option value="" disabled selected>Selecciona una categoría</option>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->categoria_id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/productosAdmin/agregarProducto.js') }}"></script>
@endpush