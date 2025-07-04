<!-- Modal Editar Producto -->
<div class="modal fade" id="editarProducto" tabindex="-1" aria-labelledby="editarProductoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditarProducto" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="codigo" id="codigo" readonly>

                    @foreach ([
                    ['label' => 'Nombre', 'type' => 'text', 'name' => 'nombre'],
                    ['label' => 'Precio', 'type' => 'number', 'name' => 'precio', 'step' => '0.01'],
                    ] as $input)
                    <label for="{{ $input['name'] }}">{{ $input['label'] }}</label>
                    <input
                        type="{{ $input['type'] }}"
                        name="{{ $input['name'] }}"
                        id="{{ $input['name'] }}"
                        class="form-control mb-2"
                        {{ isset($input['step']) ? "step={$input['step']}" : '' }}
                        required>
                    @endforeach

                    <label for="categoria">Categoría</label>
                    <select name="categoria_id" id="categoria" class="form-control mb-2" required>
                        <option value="" disabled selected>Selecciona una categoría</option>
                        @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->categoria_id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>

                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control mb-2" rows="3" required></textarea>

                    <label for="imagen">Imagen</label>
                    <input type="file" name="imagen" id="imagen" class="form-control mb-2" accept="image/*">

                    <div id="previewImagen" class="mt-2 d-none">
                        <img src="" alt="Imagen actual" style="max-width: 100%; max-height: 150px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/productosAdmin/editarProducto.js') }}"></script>
@endpush