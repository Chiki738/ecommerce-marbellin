@extends('admin.appAdmin')

@section('content')
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-box-seam"></i> Sistema de Pedidos
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.html">
                    <i class="bi bi-list-ul"></i> Pedidos
                </a>
                <a class="nav-link" href="devoluciones.html">
                    <i class="bi bi-arrow-return-left"></i> Devoluciones
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Pedido <span id="pedidoId" class="badge bg-secondary">#1001</span></h1>
                <p class="text-muted mb-0">Gestión completa del pedido</p>
            </div>
            <a href="index.html" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <!-- Estado Actual -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Estado Actual</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span id="estadoActual" class="badge bg-warning fs-6">Pendiente</span>
                                <p class="mb-0 mt-2 text-muted">Última actualización: 15/06/2023 10:30:45</p>
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
                                        <td>#1001</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha:</th>
                                        <td>15/06/2023</td>
                                    </tr>
                                    <tr>
                                        <th>Total:</th>
                                        <td>S/ 250.00</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Creado:</th>
                                        <td>15/06/2023 10:30:45</td>
                                    </tr>
                                    <tr>
                                        <th>Actualizado:</th>
                                        <td>15/06/2023 10:30:45</td>
                                    </tr>
                                    <tr>
                                        <th>Productos:</th>
                                        <td>2 items</td>
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
                                        <td>Juan Pérez</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>juan.perez@ejemplo.com</td>
                                    </tr>
                                    <tr>
                                        <th>Teléfono:</th>
                                        <td>+51 999 888 777</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Distrito:</th>
                                        <td>San Isidro</td>
                                    </tr>
                                    <tr>
                                        <th>Provincia:</th>
                                        <td>Lima</td>
                                    </tr>
                                    <tr>
                                        <th>Cliente desde:</th>
                                        <td>01/01/2023</td>
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
                            <i class="bi bi-geo-alt"></i>
                            Av. Principal 123, Dpto 502, San Isidro, Lima
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
                                    <tr>
                                        <td>P001</td>
                                        <td>Camiseta Básica</td>
                                        <td>
                                            <span class="badge bg-secondary">Talla M</span>
                                            <span class="badge bg-primary">Azul</span>
                                        </td>
                                        <td>S/ 50.00</td>
                                        <td>2</td>
                                        <td>S/ 100.00</td>
                                    </tr>
                                    <tr>
                                        <td>P002</td>
                                        <td>Pantalón Jeans</td>
                                        <td>
                                            <span class="badge bg-secondary">Talla 32</span>
                                            <span class="badge bg-dark">Negro</span>
                                        </td>
                                        <td>S/ 75.00</td>
                                        <td>2</td>
                                        <td>S/ 150.00</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th colspan="5" class="text-end">Total:</th>
                                        <th>S/ 250.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Historial de Estados -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Historial de Estados</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-warning">Pendiente</span>
                                    <span class="ms-2">Pedido registrado en el sistema</span>
                                </div>
                                <small class="text-muted">15/06/2023 10:30:45</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Acciones -->
            <div class="col-lg-4">
                <div class="action-buttons">
                    <!-- Acciones Rápidas -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">Acciones Rápidas</h5>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal">
                                <i class="bi bi-arrow-repeat"></i> Cambiar Estado
                            </button>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enviarEmailModal">
                                <i class="bi bi-envelope"></i> Enviar Email
                            </button>
                            <button class="btn btn-info" onclick="imprimirPedido()">
                                <i class="bi bi-printer"></i> Imprimir Pedido
                            </button>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelarPedidoModal">
                                <i class="bi bi-x-circle"></i> Cancelar Pedido
                            </button>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Información Adicional</h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <p><strong>Método de Pago:</strong> Tarjeta de Crédito</p>
                                <p><strong>Código de Seguimiento:</strong> TRK123456789</p>
                                <p><strong>Tiempo Estimado:</strong> 3-5 días hábiles</p>
                                <p><strong>Notas:</strong> Entrega en horario de oficina</p>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cambiar Estado -->
    <div class="modal fade" id="cambiarEstadoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="cambiarEstadoForm">
                        <div class="mb-3">
                            <label class="form-label">Estado Actual</label>
                            <input type="text" class="form-control" value="Pendiente" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nuevo Estado</label>
                            <select class="form-select" id="nuevoEstado" required>
                                <option value="">Seleccionar estado...</option>
                                <option value="procesando">Procesando</option>
                                <option value="enviado">Enviado</option>
                                <option value="entregado">Entregado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comentarios (opcional)</label>
                            <textarea class="form-control" rows="3" placeholder="Agregar comentarios sobre el cambio de estado..."></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notificarCliente" checked>
                            <label class="form-check-label" for="notificarCliente">
                                Notificar al cliente por email
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="cambiarEstado()">Cambiar Estado</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Enviar Email -->
    <div class="modal fade" id="enviarEmailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar Email al Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="enviarEmailForm">
                        <div class="mb-3">
                            <label class="form-label">Para</label>
                            <input type="email" class="form-control" value="juan.perez@ejemplo.com" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asunto</label>
                            <input type="text" class="form-control" value="Actualización de su pedido #1001" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensaje</label>
                            <textarea class="form-control" rows="6" required placeholder="Escriba su mensaje aquí...">Estimado Juan,

Esperamos que se encuentre bien. Le escribimos para informarle sobre el estado de su pedido #1001.

Saludos cordiales,
Equipo de Atención al Cliente</textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="copiaAdmin">
                            <label class="form-check-label" for="copiaAdmin">
                                Enviar copia al administrador
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="enviarEmail()">
                        <i class="bi bi-send"></i> Enviar Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Obtener ID del pedido de la URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const pedidoId = urlParams.get('id');
            if (pedidoId) {
                document.getElementById('pedidoId').textContent = `#${pedidoId}`;
                document.title = `Pedido #${pedidoId} - Detalle`;
            }
        });

        function cambiarEstado() {
            const nuevoEstado = document.getElementById('nuevoEstado').value;
            const notificar = document.getElementById('notificarCliente').checked;

            if (!nuevoEstado) {
                alert('Por favor seleccione un estado');
                return;
            }

            // Validaciones de estados
            const estadoActual = 'pendiente';
            if (!validarCambioEstado(estadoActual, nuevoEstado)) {
                alert('Cambio de estado no válido');
                return;
            }

            // Simular cambio de estado
            alert(`Estado cambiado a: ${nuevoEstado}${notificar ? ' (Cliente notificado)' : ''}`);

            // Actualizar interfaz
            const badge = document.getElementById('estadoActual');
            badge.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
            badge.className = `badge fs-6 ${getEstadoClass(nuevoEstado)}`;

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('cambiarEstadoModal')).hide();
        }

        function validarCambioEstado(actual, nuevo) {
            const transicionesValidas = {
                'pendiente': ['procesando', 'cancelado'],
                'procesando': ['enviado', 'cancelado'],
                'enviado': ['entregado'],
                'entregado': [],
                'cancelado': []
            };

            return transicionesValidas[actual]?.includes(nuevo) || false;
        }

        function getEstadoClass(estado) {
            const clases = {
                'pendiente': 'bg-warning',
                'procesando': 'bg-primary',
                'enviado': 'bg-info',
                'entregado': 'bg-success',
                'cancelado': 'bg-danger'
            };
            return clases[estado] || 'bg-secondary';
        }

        function enviarEmail() {
            alert('Email enviado correctamente al cliente');
            bootstrap.Modal.getInstance(document.getElementById('enviarEmailModal')).hide();
        }

        function imprimirPedido() {
            window.print();
        }
    </script>
</body>

</html>@endsection