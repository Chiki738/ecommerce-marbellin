@extends('admin.appAdmin')

@section('content')
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .cliente-section {
            border-left: 4px solid #007bff;
            margin-bottom: 30px;
        }

        .pedido-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .pedido-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filters-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .navbar-custom {
            background-color: #2c3e50;
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
            <h1><i class="bi bi-people"></i> Pedidos por Cliente</h1>
            <div class="badge bg-info fs-6">Total: <span id="totalPedidos">8</span> pedidos</div>
        </div>

        <!-- Filtros -->
        <div class="filters-container">
            <form id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Cliente</label>
                        <select class="form-select" id="clienteFilter">
                            <option value="">Todos los clientes</option>
                            <option value="juan">Juan Pérez</option>
                            <option value="maria">María López</option>
                            <option value="carlos">Carlos Rodríguez</option>
                            <option value="ana">Ana Martínez</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="estadoFilter">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="procesando">Procesando</option>
                            <option value="enviado">Enviado</option>
                            <option value="entregado">Entregado</option>
                            <option value="cancelado">Cancelado</option>
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

        <!-- Pedidos por Cliente -->
        <div id="pedidosContainer">
            <!-- Cliente 1: Juan Pérez -->
            <div class="cliente-section">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle"></i> Juan Pérez
                                <small class="text-light">(juan.perez@email.com)</small>
                            </h5>
                            <span class="badge bg-light text-dark">3 pedidos</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Pedido 1001 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1001)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1001</h6>
                                            <span class="badge bg-warning">Pendiente</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 15/06/2023<br>
                                                <i class="bi bi-box"></i> 2 productos<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 250.00
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Pedido 1004 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1004)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1004</h6>
                                            <span class="badge bg-success">Entregado</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 12/06/2023<br>
                                                <i class="bi bi-box"></i> 1 producto<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 180.50
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Pedido 1007 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1007)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1007</h6>
                                            <span class="badge bg-info">Enviado</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 10/06/2023<br>
                                                <i class="bi bi-box"></i> 3 productos<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 420.75
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cliente 2: María López -->
            <div class="cliente-section">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle"></i> María López
                                <small class="text-light">(maria.lopez@email.com)</small>
                            </h5>
                            <span class="badge bg-light text-dark">2 pedidos</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Pedido 1002 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1002)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1002</h6>
                                            <span class="badge bg-primary">Procesando</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 16/06/2023<br>
                                                <i class="bi bi-box"></i> 1 producto<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 180.50
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Pedido 1005 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1005)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1005</h6>
                                            <span class="badge bg-success">Entregado</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 14/06/2023<br>
                                                <i class="bi bi-box"></i> 2 productos<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 320.00
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cliente 3: Carlos Rodríguez -->
            <div class="cliente-section">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle"></i> Carlos Rodríguez
                                <small class="text-light">(carlos.rodriguez@email.com)</small>
                            </h5>
                            <span class="badge bg-light text-dark">2 pedidos</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Pedido 1003 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1003)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1003</h6>
                                            <span class="badge bg-danger">Cancelado</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 17/06/2023<br>
                                                <i class="bi bi-box"></i> 1 producto<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 320.75
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Pedido 1006 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1006)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1006</h6>
                                            <span class="badge bg-info">Enviado</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 13/06/2023<br>
                                                <i class="bi bi-box"></i> 2 productos<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 275.25
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cliente 4: Ana Martínez -->
            <div class="cliente-section">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle"></i> Ana Martínez
                                <small class="text-dark">(ana.martinez@email.com)</small>
                            </h5>
                            <span class="badge bg-dark text-light">1 pedido</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Pedido 1008 -->
                            <div class="col-md-4">
                                <div class="pedido-card card h-100 border-0" onclick="verDetalle(1008)">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title">#1008</h6>
                                            <span class="badge bg-warning">Pendiente</span>
                                        </div>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 18/06/2023<br>
                                                <i class="bi bi-box"></i> 1 producto<br>
                                                <i class="bi bi-currency-dollar"></i> S/ 150.25
                                            </small>
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verDetalle(pedidoId) {
            window.location.href = `detalle.html?id=${pedidoId}`;
        }

        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de filtrado
            const cliente = document.getElementById('clienteFilter').value;
            const estado = document.getElementById('estadoFilter').value;
            const fechaDesde = document.getElementById('fechaDesde').value;
            const fechaHasta = document.getElementById('fechaHasta').value;

            console.log('Filtros aplicados:', {
                cliente,
                estado,
                fechaDesde,
                fechaHasta
            });
            // Implementar lógica de filtrado aquí
        });

        document.getElementById('filterForm').addEventListener('reset', function() {
            // Mostrar todos los pedidos
            console.log('Filtros limpiados');
        });
    </script>
</body>

</html>

@endsection