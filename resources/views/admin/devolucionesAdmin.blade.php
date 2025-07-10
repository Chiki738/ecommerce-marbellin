@extends('layouts.app')

@section('title', 'Solicitudes de Cambio de Productos')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-primary">
        <i class="fas fa-exchange-alt me-2"></i>Solicitudes de Cambio de Productos
    </h2>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th>Código</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cambios as $solicitud)
                <tr>
                    <td>#{{ $solicitud->id }}</td>
                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $solicitud->pedido?->cliente?->nombre ?? '—' }}</strong><br>
                        <small class="text-muted">{{ $solicitud->pedido?->cliente?->email ?? '—' }}</small>
                    </td>
                    <td>
                        {{ $solicitud->detalle?->producto?->nombre ?? '—' }}<br>
                        <span class="badge bg-secondary">
                            {{ $solicitud->detalle?->variante?->talla ?? '—' }} / {{ $solicitud->detalle?->variante?->color ?? '—' }}
                        </span>
                    </td>
                    <td class="text-start">
                        <span class="text-muted fst-italic">{{ $solicitud->comentario_cliente ?? '—' }}</span>
                    </td>
                    <td>
                        @if ($solicitud->estado === null || $solicitud->estado === 'Pendiente')
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i>Pendiente
                        </span>
                        @elseif ($solicitud->estado === 'Cambiado')
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Cambiado
                        </span>
                        @elseif ($solicitud->estado === 'Rechazado')
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle me-1"></i>Rechazado
                        </span>
                        @else
                        <span class="badge bg-secondary">{{ $solicitud->estado }}</span>
                        @endif
                    </td>
                    <td>
                        <button
                            onclick="abrirModal(this)"
                            data-cambio='@json($solicitud)'
                            class="btn btn-sm btn-info text-white">
                            <i class="fas fa-eye"></i> Ver detalle
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-muted">No hay solicitudes de cambio registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal separado -->
@include('admin.pedidos.modales.modalDevolucionesAdmin')
@endsection

@push('scripts')
<script src="{{ asset('js/devolucionesAdmin.js') }}"></script>
@endpush