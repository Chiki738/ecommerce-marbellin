<!-- Modal -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarProductoLabel">Agregar producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <!-- Mostrar errores -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <input type="text" name="codigo" placeholder="Código" class="form-control mb-2" required>
                    <input type="text" name="nombre" placeholder="Nombre" class="form-control mb-2" required>
                    <input type="number" step="0.01" name="precio" placeholder="Precio" class="form-control mb-2" required>
                    <textarea name="descripcion" placeholder="Descripción" class="form-control mb-2" required></textarea>
                    <input type="file" name="imagen" class="form-control mb-2" required>

                    <select name="categoria" id="categoria" required class="form-control mb-2">
                        <option value="" disabled selected>Selecciona una categoría</option>
                        <option value="bikinis">Bikini</option>
                        <option value="cacheteros">Cachetero</option>
                        <option value="semi_hilos">Semi Hilos</option>
                        <option value="otros_productos">Otros Productos</option>
                    </select>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>

            </form>

        </div>
    </div>
</div>