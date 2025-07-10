{{-- archivo: resources/views/client/historialPedidos.blade.php --}}

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
                $puedeSolicitar = $pedido->estado->nombre === 'entregado' && $pedido->fecha->diffInDays(now()) <= 2;
                    $agrupados=$pedido->detalles->groupBy('producto.nombre');
                    @endphp
                    <tr>
                        <td><strong>PED-{{ $pedido->id }}</strong></td>
                        <td>{{ $pedido->fecha->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ match($pedido->estado->nombre) {
                            'entregado' => 'success',
                            'enviado' => 'warning',
                            'procesando' => 'info',
                            'cancelado' => 'danger',
                            default => 'secondary'
                        } }}">
                                {{ ucfirst($pedido->estado->nombre) }}
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
    document.querySelectorAll('.btn-change').forEach(btn => {
        btn.addEventListener('click', function() {
            const pedidoId = this.dataset.id;
            const detalles = JSON.parse(this.dataset.detalles);
            const select = document.getElementById('detalleSelect');
            select.innerHTML = '<option value="">Selecciona una variante</option>';

            detalles.forEach(d => {
                const text = `${d.producto.nombre} - Color: ${d.variante.color}, Talla: ${d.variante.talla} (Cant: ${d.cantidad})`;
                const option = document.createElement('option');
                option.value = d.id;
                option.textContent = text;
                option.setAttribute('data-variante-id', d.variante_id);
                select.appendChild(option);
            });

            document.getElementById('pedidoInput').value = pedidoId;
        });

        document.getElementById('detalleSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('varianteInput').value = selectedOption.getAttribute('data-variante-id') || '';
        });
    });
</script>
@endpush
@endsection