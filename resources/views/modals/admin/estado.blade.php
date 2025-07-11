<!-- Modal cambiar estado -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-labelledby="cambiarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCambiarEstado" action="{{ route('admin.pedido.cambiarEstado', $pedido->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarEstadoLabel">Cambiar estado del pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="estado_id" class="form-select">
                        @foreach ($estados as $estado)
                        @if (strtolower($estado->nombre) !== 'cancelado')
                        <option value="{{ $estado->id }}" {{ $pedido->estado_id == $estado->id ? 'selected' : '' }}>
                            {{ ucfirst($estado->nombre) }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>