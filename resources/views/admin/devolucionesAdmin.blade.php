@extends('admin.appAdmin')

@section('content')
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Devoluciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .navbar-custom {
            background-color: #2c3e50;
        }

        .devolucion-card {
            transition: all 0.3s ease;
        }

        .devolucion-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filters-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
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
                <a class="nav-link active" href="devoluciones.html">
                    <i class="bi bi-arrow-return-left"></i> Devoluciones
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-arrow-return-left"></i> Gestión de Devoluciones</h1>
            <div class="badge bg-warning fs-6">Pendientes: <span id="totalPendientes">3</span></div>
        </div>

        <!-- Filtros -->
        <div class="filters-container">
            <form id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="estadoFilter">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aprobada">Aprobada</option>
                            <option value="rechazada">Rechazada</option>
                            <option value="procesada">Procesada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" id="tipoFilter">
                            <option value="">Todos los tipos</option>
                            <option value="devolucion">Devolución</option>
                            <option value="reclamo">Reclamo</option>
                            <option value="cambio">Cambio</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" id="fechaDesde">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" id="fechaHasta">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista de Devoluciones -->
        <div class="row" id="devolucionesContainer">
            <!-- Devolución 1 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="devolucion-card card h-100">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">DEV-001</h6>
                            <span class="badge bg-danger">Pendiente</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Pedido:</strong> #1001<br>
                            <strong>Cliente:</strong> Juan Pérez<br>
                            <strong>Tipo:</strong> Devolución<br>
                            <strong>Fecha:</strong> 20/06/2023<br>
                            <strong>Motivo:</strong> Producto defectuoso
                        </p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="verDevolucion('DEV-001')">
                                <i class="bi bi-eye"></i> Ver Detalle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Devolución 2 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="devolucion-card card h-100">
                    <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">REC-002</h6>
                            <span class="badge bg-warning">Pendiente</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Pedido:</strong> #1002<br>
                            <strong>Cliente:</strong> María López<br>
                            <strong>Tipo:</strong> Reclamo<br>
                            <strong>Fecha:</strong> 19/06/2023<br>
                            <strong>Motivo:</strong> Entrega tardía
                        </p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="verDevolucion('REC-002')">
                                <i class="bi bi-eye"></i> Ver Detalle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Devolución 3 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="devolucion-card card h-100">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">CAM-003</h6>
                            <span class="badge bg-primary">Aprobada</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Pedido:</strong> #1003<br>
                            <strong>Cliente:</strong> Carlos Rodríguez<br>
                            <strong>Tipo:</strong> Cambio<br>
                            <strong>Fecha:</strong> 18/06/2023<br>
                            <strong>Motivo:</strong> Talla incorrecta
                        </p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="verDevolucion('CAM-003')">
                                <i class="bi bi-eye"></i> Ver Detalle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Devolución 4 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="devolucion-card card h-100">
                    <div class="card-header bg-danger text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">DEV-004</h6>
                            <span class="badge bg-secondary">Rechazada</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Pedido:</strong> #1004<br>
                            <strong>Cliente:</strong> Ana Martínez<br>
                            <strong>Tipo:</strong> Devolución<br>
                            <strong>Fecha:</strong> 17/06/2023<br>
                            <strong>Motivo:</strong> No me gustó
                        </p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="verDevolucion('DEV-004')">
                                <i class="bi bi-eye"></i> Ver Detalle
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle de Devolución -->
    <div class="modal fade" id="devolucionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Devolución <span id="devolucionId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información General</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th>ID:</th>
                                    <td id="modalId">DEV-001</td>
                                </tr>
                                <tr>
                                    <th>Pedido:</th>
                                    <td id="modalPedido">#1001</td>
                                </tr>
                                <tr>
                                    <th>Cliente:</th>
                                    <td id="modalCliente">Juan Pérez</td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td id="modalTipo">Devolución</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td><span id="modalEstado" class="badge bg-warning">Pendiente</span></td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td id="modalFecha">20/06/2023</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Productos Involucrados</h6>
                            <div id="modalProductos">
                                <div class="border p-2 mb-2 rounded">
                                    <strong>Camiseta Básica</strong><br>
                                    <small>Código: P001 | Talla: M | Color: Azul</small><br>
                                    <small>Cantidad: 1 | Precio: S/ 50.00</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6>Motivo de la Solicitud</h6>
                        <p id="modalMotivo" class="bg-light p-3 rounded">
                            El producto llegó con defectos de fabricación. La costura de la manga derecha está descosida y presenta manchas que no se pueden quitar.
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6>Evidencias</h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-info">📷 imagen1.jpg</span>
                            <span class="badge bg-info">📷 imagen2.jpg</span>
                        </div>
                    </div>

                    <div class="mb-3" id="comentariosAdmin" style="display: none;">
                        <h6>Comentarios del Administrador</h6>
                        <p class="bg-light p-3 rounded" id="modalComentarios"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group w-100" id="accionesDevolucion">
                        <button type="button" class="btn btn-success" onclick="aprobarDevolucion()">
                            <i class="bi bi-check-circle"></i> Aprobar
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rechazarDevolucion()">
                            <i class="bi bi-x-circle"></i> Rechazar
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comentariosModal">
                            <i class="bi bi-chat-text"></i> Comentar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Comentarios -->
    <div class="modal fade" id="comentariosModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Comentarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="comentariosForm">
                        <div class="mb-3">
                            <label class="form-label">Acción</label>
                            <select class="form-select" id="accionSelect" required>
                                <option value="">Seleccionar acción...</option>
                                <option value="aprobar">Aprobar solicitud</option>
                                <option value="rechazar">Rechazar solicitud</option>
                                <option value="solicitar_info">Solicitar más información</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comentarios</label>
                            <textarea class="form-control" rows="4" id="comentariosTexto" required placeholder="Escriba sus comentarios aquí..."></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notificarClienteDevolucion" checked>
                            <label class="form-check-label" for="notificarClienteDevolucion">
                                Notificar al cliente por email
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="procesarDevolucion()">
                        <i class="bi bi-send"></i> Procesar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verDevolucion(id) {
            document.getElementById('devolucionId').textContent = id;
            document.getElementById('modalId').textContent = id;

            // Aquí cargarías los datos reales de la devolución
            const modal = new bootstrap.Modal(document.getElementById('devolucionModal'));
            modal.show();
        }

        function aprobarDevolucion() {
            if (confirm('¿Está seguro de aprobar esta devolución?')) {
                alert('Devolución aprobada correctamente');
                bootstrap.Modal.getInstance(document.getElementById('devolucionModal')).hide();
            }
        }

        function rechazarDevolucion() {
            if (confirm('¿Está seguro de rechazar esta devolución?')) {
                alert('Devolución rechazada');
                bootstrap.Modal.getInstance(document.getElementById('devolucionModal')).hide();
            }
        }

        function procesarDevolucion() {
            const accion = document.getElementById('accionSelect').value;
            const comentarios = document.getElementById('comentariosTexto').value;
            const notificar = document.getElementById('notificarClienteDevolucion').checked;

            if (!accion || !comentarios) {
                alert('Por favor complete todos los campos');
                return;
            }

            alert(`Devolución procesada: ${accion}${notificar ? ' (Cliente notificado)' : ''}`);

            // Cerrar modales
            bootstrap.Modal.getInstance(document.getElementById('comentariosModal')).hide();
            bootstrap.Modal.getInstance(document.getElementById('devolucionModal')).hide();
        }

        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Implementar lógica de filtrado
            console.log('Filtros aplicados');
        });
    </script>
</body>

</html>@endsection