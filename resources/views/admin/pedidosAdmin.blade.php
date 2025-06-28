@extends('admin.appAdmin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <h2><i class="bi bi-list-check"></i> Gestión de Pedidos</h2>
            <p class="text-muted">Administra todos los pedidos y actualiza su estado</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>0</h4>
                            <p class="mb-0">Pendientes</p>
                        </div>
                        <i class="bi bi-clock display-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>0</h4>
                            <p class="mb-0">Procesando</p>
                        </div>
                        <i class="bi bi-gear display-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>0</h4>
                            <p class="mb-0">Enviados</p>
                        </div>
                        <i class="bi bi-truck display-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>0</h4>
                            <p class="mb-0">Entregados</p>
                        </div>
                        <i class="bi bi-check-circle display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Pedidos</h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Exportar</button>
                        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-printer"></i> Imprimir</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí insertarás pedidos dinámicamente con Blade o JS -->
                                <tr>
                                    <td><strong>#PED-0001</strong></td>
                                    <td>Juan Pérez</td>
                                    <td>26 Jun 2025</td>
                                    <td>Bikini Rojo (1), Brasier Negro (2)</td>
                                    <td>S/ 99.00</td>
                                    <td>
                                        <select class="form-select form-select-sm">
                                            <option value="1">Pendiente</option>
                                            <option value="2">Procesando</option>
                                            <option value="3">Enviado</option>
                                            <option value="4">Entregado</option>
                                            <option value="5">Cancelado</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info"><i class="bi bi-eye"></i></button>
                                            <button class="btn btn-outline-primary"><i class="bi bi-printer"></i></button>
                                            <button class="btn btn-outline-success"><i class="bi bi-envelope"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item disabled"><a class="page-link">Anterior</a></li>
                            <li class="page-item active"><a class="page-link">1</a></li>
                            <li class="page-item"><a class="page-link">2</a></li>
                            <li class="page-item"><a class="page-link">Siguiente</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection