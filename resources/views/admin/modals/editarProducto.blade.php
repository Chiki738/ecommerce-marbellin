<!-- Modal Editar Producto -->
<div class="modal fade" id="editarProducto" tabindex="-1" aria-labelledby="editarProductoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditarProducto" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarProductoLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="codigo" id="codigo" readonly>

                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control mb-2" required>

                    <label for="precio">Precio</label>
                    <input type="number" step="0.01" name="precio" id="precio" class="form-control mb-2" required>

                    <label for="categoria">Categoría</label>
                    <select name="categoria_id" id="categoria" class="form-control mb-2" required>
                        <option value="" disabled>Selecciona una categoría</option>
                        @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->categoria_id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>

                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control mb-2" rows="3" required></textarea>

                    <label for="imagen">Imagen</label>
                    <input type="file" name="imagen" id="imagen" class="form-control mb-2" accept="image/*">

                    <div id="previewImagen" class="mt-2">
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

<!-- Script para manejar la carga de datos al modal -->
<script>
    document.querySelectorAll('.btnEditarProducto').forEach(btn => {
        btn.addEventListener('click', () => {
            const codigo = btn.getAttribute('data-codigo');
            const nombre = btn.getAttribute('data-nombre');
            const precio = btn.getAttribute('data-precio');
            const categoria = btn.getAttribute('data-categoria');
            const descripcion = btn.getAttribute('data-descripcion');
            const imagenUrl = btn.getAttribute('data-imagen');

            const form = document.getElementById('formEditarProducto');
            form.action = `/productos/${codigo}`;

            form.codigo.value = codigo;
            form.nombre.value = nombre;
            form.precio.value = precio;
            form.categoria.value = categoria; // Asegúrate de que "categoria" sea el ID, no el nombre
            form.descripcion.value = descripcion;

            const preview = document.querySelector('#previewImagen img');
            if (imagenUrl) {
                preview.src = imagenUrl;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }

            const modal = new bootstrap.Modal(document.getElementById('editarProducto'));
            modal.show();
        });
    });

    document.getElementById('imagen').addEventListener('change', e => {
        const preview = document.querySelector('#previewImagen img');
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });
</script>