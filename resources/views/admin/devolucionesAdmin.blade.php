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

    @php
    $estados = ['Todas', 'Pendiente', 'Aprobado', 'Rechazado', 'Cambiado'];
    $colores = [
    'Todas' => 'btn-dark',
    'Pendiente' => 'btn-warning text-dark',
    'Aprobado' => 'btn-success',
    'Rechazado' => 'btn-danger',
    'Cambiado' => 'btn-primary'
    ];
    @endphp

    <div class="mb-4 d-flex flex-wrap gap-2">
        @foreach ($estados as $estadoBtn)
        <a href="{{ route('admin.cambios.index', ['estado' => $estadoBtn]) }}"
            class="btn {{ $colores[$estadoBtn] }} {{ (request('estado') ?? 'Todas') === $estadoBtn ? 'active shadow' : '' }}">
            {{ $estadoBtn }}
            ({{ $estadoBtn === 'Todas' 
                ? \App\Models\CambioProducto::count() 
                : \App\Models\CambioProducto::where('estado', $estadoBtn)->count() }})
        </a>
        @endforeach
    </div>

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
                <tr id="fila-cambio-{{ $solicitud->id }}">
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
                    <td class="estado-solicitud">
                        @switch($solicitud->estado)

                        @case('Pendiente')
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i>Pendiente
                        </span>
                        @break
                        @case('Aprobado')
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Aprobado
                        </span>
                        @break
                        @case('Rechazado')
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle me-1"></i>Rechazado
                        </span>
                        @break
                        @case('Cambiado')
                        <span class="badge bg-primary">
                            <i class="fas fa-cogs me-1"></i>Cambiado
                        </span>
                        @break
                        @default
                        <span class="badge bg-secondary">{{ $solicitud->estado ?? 'Sin estado' }}</span>
                        @endswitch
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

    <div class="d-flex flex-column align-items-center mt-3">
        <p class="text-muted mb-1">
            Mostrando {{ $cambios->firstItem() }} al {{ $cambios->lastItem() }} de {{ $cambios->total() }} resultados
        </p>
        {{ $cambios->withQueryString()->links() }}
    </div>
</div>

@include('modals.admin.modalDevolucionesAdmin')
@endsection

@push('scripts')
<script src="{{ asset('js/admin/devolucionesAdmin.js') }}"></script>
@endpush