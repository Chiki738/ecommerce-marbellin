{{-- resources/views/client/historialPedidos.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4" style="min-height: 100vh;">
    <h2>Historial de Pedidos</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Productos</th>
                    <th>Total</th>
                    <th>Cambio</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $pedido)
                @php
                $estadoNombre = $pedido->estado->nombre;
                $puedeSolicitar = $estadoNombre === 'entregado' && $pedido->fecha->diffInDays(now()) <= 2;
                    $agrupados=$pedido->detalles->groupBy('producto.nombre');

                    $badgeClass = match($estadoNombre) {
                    'entregado' => 'success',
                    'enviado' => 'warning',
                    'procesando' => 'info',
                    'cancelado' => 'danger',
                    default => 'secondary'
                    };
                    @endphp

                    <tr>
                        <td><strong>PED-{{ $pedido->id }}</strong></td>
                        <td>{{ $pedido->fecha->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ ucfirst($estadoNombre) }}
                            </span>
                        </td>
                        <td>
                            @foreach ($agrupados as $nombreProducto => $variantes)
                            <strong>{{ $nombreProducto }}</strong>
                            <ul class="mb-2 small text-muted ps-3">
                                @foreach ($variantes as $detalle)
                                <li>
                                    Color: {{ $detalle->variante->color }} |
                                    Talla: {{ $detalle->variante->talla }} |
                                    Cant: {{ $detalle->cantidad }}
                                </li>
                                @endforeach
                            </ul>
                            @endforeach
                        </td>
                        <td><strong>S/ {{ number_format($pedido->total, 2) }}</strong></td>
                        <td>
                            @if ($puedeSolicitar)
                            <button class="btn btn-outline-primary btn-change"
                                data-bs-toggle="modal"
                                data-bs-target="#modalSolicitudCambio"
                                data-id="{{ $pedido->id }}"
                                data-detalles='@json($pedido->detalles)'>
                                <i class="bi bi-arrow-repeat me-1"></i>Solicitar Cambio
                            </button>
                            @else
                            <span class="text-muted small">No disponible</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $pedidos->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>

@include('modals.cambioProducto')

@push('scripts')
<script>
    const detalleSelect = document.getElementById('detalleSelect');
    const varianteInput = document.getElementById('varianteInput');
    const pedidoInput = document.getElementById('pedidoInput');

    document.querySelectorAll('.btn-change').forEach(btn => {
        btn.addEventListener('click', () => {
            const detalles = JSON.parse(btn.dataset.detalles || '[]');
            pedidoInput.value = btn.dataset.id;

            detalleSelect.innerHTML = '<option value="">Selecciona una variante</option>';
            detalles.forEach(({
                id,
                producto,
                variante,
                cantidad
            }) => {
                const option = new Option(
                    `${producto.nombre} - Color: ${variante.color}, Talla: ${variante.talla} (Cant: ${cantidad})`,
                    id
                );
                option.dataset.varianteId = variante.id;
                detalleSelect.appendChild(option);
            });
        });
    });

    detalleSelect.addEventListener('change', function() {
        const selected = this.selectedOptions[0];
        varianteInput.value = selected?.dataset.varianteId || '';
    });
</script>
@endpush
@endsection