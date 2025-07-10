<div class="cliente-section mb-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle"></i>
                    @if(isset($cliente))
                    {{ $cliente->nombre ?? 'Sin nombre' }} {{ $cliente->apellido ?? '' }}
                    <small class="text-light">({{ $cliente->email ?? 'Sin correo' }})</small>
                    @else
                    Resultados de pedidos filtrados
                    @endif
                </h5>
                <span class="badge bg-light text-dark">{{ $pedidos->count() }} pedido(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                @foreach($pedidos as $pedido)
                <div class="col-md-4">
                    <div class="pedido-card card h-100 border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title">#{{ $pedido->id }}</h6>
                                <span class="badge 
                                    @switch($pedido->estado_id)
                                        @case(1) bg-warning @break
                                        @case(2) bg-primary @break
                                        @case(3) bg-info @break
                                        @case(4) bg-success @break
                                        @case(5) bg-danger @break
                                        @default bg-secondary
                                    @endswitch">
                                    {{ $pedido->estadoTexto($pedido->estado_id) }}
                                </span>
                            </div>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y') }}<br>
                                    <i class="bi bi-box"></i> {{ $pedido->detalles->count() }} productos<br>
                                    <i class="bi bi-currency-dollar"></i> S/ {{ number_format($pedido->total, 2) }}
                                </small>
                            </p>
                            <button onclick="verDetalle('{{ $pedido->id }}')" class="btn btn-sm btn-outline-primary">
                                Ver Detalle
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function verDetalle(pedidoId) {
        window.location.href = `/admin/pedidos/${pedidoId}`;
    }
</script>
@endpush