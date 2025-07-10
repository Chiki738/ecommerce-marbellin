<div class="modal fade" id="modalProcesar" tabindex="-1" aria-labelledby="modalProcesarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Procesar Solicitud - <span id="codigoSolicitud">---</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Información General -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="40%">Código:</th>
                                        <td id="modalCodigo">---</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha:</th>
                                        <td id="modalFecha">---</td>
                                    </tr>
                                    <tr>
                                        <th>Cliente:</th>
                                        <td id="modalCliente">---</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td id="modalEmail">---</td>
                                    </tr>
                                    <tr>
                                        <th>Estado Actual:</th>
                                        <td><span id="modalEstado" class="badge bg-warning">---</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Producto Original -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-box me-2"></i>Producto Original</h6>
                            </div>
                            <div class="card-body">
                                <div id="productoOriginalInfo">
                                    <div><strong>---</strong></div>
                                    <div class="text-muted">Talla: —</div>
                                    <div class="text-muted">Color: —</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producto Reemplazado -->
                <div class="row" id="seccionProductoReemplazado" style="display: none;">
                    <div class="col-md-6 offset-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-box-open me-2"></i>Producto Reemplazado</h6>
                            </div>
                            <div class="card-body">
                                <div id="productoNuevoInfo">
                                    <div><strong>---</strong></div>
                                    <div class="text-muted">Talla: —</div>
                                    <div class="text-muted">Color: —</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Motivo -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-comment me-2"></i>Motivo de la Solicitud</h6>
                    </div>
                    <div class="card-body">
                        <p id="motivoCliente" class="mb-0">---</p>
                    </div>
                </div>

                <!-- Procesamiento -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Procesar Solicitud</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Acción a Realizar</label>
                                <select class="form-select" id="accionProcesar">
                                    <option value="">Seleccionar acción...</option>
                                    <option value="aprobar">Aprobar Solicitud</option>
                                    <option value="rechazar">Rechazar Solicitud</option>
                                    <option value="cambiar">Procesar Cambio de Producto</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="seccionNuevoProducto" style="display: none">
                                <label class="form-label">Nuevo Producto</label>
                                <select class="form-select" id="nuevoProducto">
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($productos as $producto)
                                    <option value="{{ $producto->nombre }}" data-id="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3" id="seccionVariantes" style="display: none">
                            <div class="col-md-6">
                                <label class="form-label">Nueva Talla</label>
                                <select class="form-select" id="nuevaTalla">
                                    <option value="">Seleccionar...</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nuevo Color</label>
                                <select class="form-select" id="nuevoColor">
                                    <option value="">Seleccionar...</option>
                                    <option value="Negro">Negro</option>
                                    <option value="Blanco">Blanco</option>
                                    <option value="Rojo">Rojo</option>
                                    <option value="Amarillo">Amarillo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3" id="comentarioAdminSeccion" style="display: none;">
                            <label class="form-label">Comentarios del Administrador</label>
                            <textarea class="form-control" id="comentarioAdmin" rows="3" placeholder="Agregar comentarios sobre la decisión..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="procesarSolicitud()">
                    <i class="fas fa-save me-1"></i>Procesar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('accionProcesar').addEventListener('change', function() {
        const accion = this.value;

        document.getElementById('seccionNuevoProducto').style.display = (accion === 'cambiar') ? 'block' : 'none';
        document.getElementById('seccionVariantes').style.display = (accion === 'cambiar') ? 'block' : 'none';
        document.getElementById('comentarioAdminSeccion').style.display = (accion === 'rechazar' || accion === 'cambiar') ? 'block' : 'none';
    });

    function procesarSolicitud() {
        const idCambio = document.getElementById('modalProcesar').dataset.id;
        const estadoSeleccionado = document.getElementById('accionProcesar').value;
        const comentarioAdmin = document.getElementById('comentarioAdmin').value;
        let varianteNuevaId = null;

        if (estadoSeleccionado === 'cambiar') {
            const productoId = document.querySelector('#nuevoProducto option:checked')?.dataset.id;
            const talla = document.getElementById('nuevaTalla').value;
            const color = document.getElementById('nuevoColor').value;

            if (!productoId || !talla || !color) {
                Swal.fire('Faltan datos', 'Completa todos los campos del nuevo producto.', 'warning');
                return;
            }

            varianteNuevaId = productoId + '-' + talla + '-' + color;
        }

        fetch(`/admin/cambios/${idCambio}/procesar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    estado: estadoSeleccionado === 'aprobar' ? 'Aprobado' : estadoSeleccionado === 'rechazar' ? 'Rechazado' : 'Cambiado',
                    comentario_admin: comentarioAdmin,
                    variante_nueva_id: varianteNuevaId,
                    notificar: true // 👈 agrega esto
                })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    location.reload();
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
            });
    }
</script>