@extends('layouts.app')

@section('content')
@php
function getEstadoClass($estado) {
return match ($estado) {
'pendiente' => 'bg-warning',
'procesando' => 'bg-primary',
'enviado' => 'bg-info',
'entregado' => 'bg-success',
'cancelado' => 'bg-danger',
default => 'bg-secondary',
};
}
@endphp

<style>
    .navbar-custom {
        background-color: #2c3e50;
    }

    .action-buttons {
        position: sticky;
        top: 20px;
    }

    .timeline-item {
        border-left: 3px solid #007bff;
        padding-left: 20px;
        margin-bottom: 20px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #007bff;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Pedido <span id="pedidoId" class="badge bg-secondary">#{{ $pedido->id }}</span></h1>
            <p class="text-muted mb-0">Gestión completa del pedido</p>
        </div>
        <a href="{{ route('admin.pedidosAdmin') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Estado Actual -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Estado Actual</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span id="estadoActual" class="badge fs-6 {{ getEstadoClass($pedido->estado->nombre) }}">
                                {{ ucfirst($pedido->estado->nombre) }}
                            </span>
                            <p class="mb-0 mt-2 text-muted">Última actualización: {{ $pedido->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal">
                            <i class="bi bi-arrow-repeat"></i> Cambiar Estado
                        </button>
                    </div>
                </div>
            </div>

            <!-- Información del Pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>ID del Pedido:</th>
                                    <td>#{{ $pedido->id }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ $pedido->fecha->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td>S/ {{ number_format($pedido->total, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Creado:</th>
                                    <td>{{ $pedido->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Actualizado:</th>
                                    <td>{{ $pedido->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Productos:</th>
                                    <td>{{ count($pedido->detalles) }} items</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $pedido->cliente->nombre }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $pedido->cliente->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Distrito:</th>
                                    <td>{{ $pedido->cliente->distrito->nombre ?? 'No disponible' }}</td>
                                </tr>
                                <tr>
                                    <th>Provincia:</th>
                                    <td>{{ $pedido->cliente->distrito->provincia->nombre ?? 'No disponible' }}</td>
                                </tr>
                                <tr>
                                    <th>Cliente desde:</th>
                                    <td>{{ $pedido->cliente->created_at->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dirección de Envío -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Dirección de Envío</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <i class="bi bi-geo-alt"></i> {{ $pedido->direccion_envio }}
                    </p>
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Productos del Pedido</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Variante</th>
                                    <th>Precio Unit.</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto->codigo }}</td>
                                    <td>{{ $detalle->producto->nombre }}</td>
                                    <td>
                                        <span class="badge bg-secondary">Talla {{ $detalle->variante->talla }}</span>
                                        <span class="badge bg-primary">{{ $detalle->variante->color }}</span>
                                    </td>
                                    <td>S/ {{ number_format($detalle->precio_unit, 2) }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th>S/ {{ number_format($pedido->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Acciones -->
        <div class="col-lg-4">
            <div class="action-buttons">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Acciones Rápidas</h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal">
                            <i class="bi bi-arrow-repeat"></i> Cambiar Estado
                        </button>
                        <a href="{{ route('admin.pedidos.imprimir', $pedido->id) }}" class="btn btn-info">
                            <i class="bi bi-printer"></i> Imprimir Pedido
                        </a>
                        <button class="btn btn-outline-danger" onclick="confirmarCancelacion('{{ $pedido->id }}')">
                            <i class="bi bi-x-circle"></i> Cancelar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals.admin.estado')

@push('scripts')
<script>
    function setupAjax(formId, onSuccess) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(form.action, {
                    method: form.method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Éxito', data.message, 'success');
                        onSuccess(data);
                    } else {
                        Swal.fire('Error', 'Ocurrió un problema', 'error');
                    }
                    bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                });
        }); 
    }

    setupAjax('formCambiarEstado', res => {
        document.getElementById('estadoActual').innerText = res.estado;
        document.getElementById('estadoActual').className = 'badge fs-6 ' + res.clase;
    });

    function confirmarCancelacion(pedidoId) {
        if (!pedidoId) {
            Swal.fire('Error', 'ID de pedido no válido.', 'error');
            return;
        }

        Swal.fire({
            title: '¿Cancelar pedido?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, volver',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/pedidos/${pedidoId}/cancelar`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Cancelado', data.message, 'success');
                            const badge = document.getElementById('estadoActual');
                            badge.innerText = 'Cancelado';
                            badge.className = 'badge fs-6 bg-danger';
                        } else {
                            Swal.fire('Error', 'No se pudo cancelar el pedido.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Ocurrió un error al cancelar.', 'error');
                    });
            }
        });
    }
</script>
@endpush
@endsection