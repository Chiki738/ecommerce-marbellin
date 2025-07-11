{{-- resources/views/modals/cambioProducto.blade.php --}}
<div class="modal fade" id="modalSolicitudCambio" tabindex="-1" aria-labelledby="cambioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCambioProducto">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cambioLabel">Solicitar Cambio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pedido_id" id="pedidoInput">
                    <input type="hidden" name="variante_id" id="varianteInput">

                    <div class="mb-3">
                        <label for="detalleSelect" class="form-label">Producto a cambiar</label>
                        <select name="detalle_pedido_id" id="detalleSelect" class="form-select" required>
                            <option value="">Selecciona una opción</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comentario" class="form-label">Motivo del cambio</label>
                        <textarea name="comentario" id="comentario" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formCambioProducto');
        const modalElement = document.getElementById('modalSolicitudCambio');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);

            try {
                const response = await fetch(`{{ route('cambio.solicitar') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token')
                    },
                    body: formData
                });

                const result = await response.json();

                Swal.fire({
                    icon: response.ok ? 'success' : 'error',
                    title: response.ok ? '¡Éxito!' : 'Error',
                    text: result.message || (response.ok ? 'Cambio solicitado correctamente.' : 'Ocurrió un error.')
                });

                if (response.ok) {
                    form.reset();
                    bootstrap.Modal.getInstance(modalElement)?.hide();
                }

            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Error de red o del servidor.', 'error');
            }
        });
    });
</script>
@endpush