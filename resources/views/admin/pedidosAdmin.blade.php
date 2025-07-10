@extends('layouts.app')

@section('content')
<div class="container mt-4 pb-4">
    <!-- Título -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-people"></i> Gestión de Pedidos por Cliente</h1>
        <span class="badge bg-info fs-6">Filtra para ver pedidos</span>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar por correo...">
                <input type="number" id="buscarIdPedido" class="form-control" placeholder="ID del pedido">
            </div>
        </div>
        <div class="col-md-3">
            <select id="filtroEstado" class="form-select">
                <option value="">Todos los estados</option>
                <option value="1">Pendiente</option>
                <option value="2">En Proceso</option>
                <option value="3">Enviado</option>
                <option value="4">Entregado</option>
                <option value="5">Cancelado</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="filtroFecha" class="form-select">
                <option value="">Todas las fechas</option>
                <option value="hoy">Hoy</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mes</option>
            </select>
        </div>
        <div class="col-md-2">
            <button id="btnBuscar" class="btn btn-primary">Buscar</button>
            <button id="btnLimpiarFiltros" class="btn btn-secondary">Limpiar</button>
        </div>
    </div>

    <!-- Resultados por página -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <select id="resultadosPorPagina" class="form-select form-select-sm" style="width: auto;">
                <option value="5">5 por página</option>
                <option value="10" selected>10 por página</option>
                <option value="20">20 por página</option>
            </select>
        </div>
        <div id="infoResultados" class="text-muted small"></div>
    </div>

    <!-- Contenedor de pedidos -->
    <div id="pedidosContainer"></div>

    <!-- Sin resultados -->
    <div id="sinResultados" class="alert alert-warning text-center" style="display: none;">
        <i class="bi bi-exclamation-triangle"></i> No se encontraron pedidos con los filtros aplicados.
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnBuscar = document.getElementById('btnBuscar');

        btnBuscar.addEventListener('click', function() {
            const idPedido = document.getElementById('buscarIdPedido').value.trim();
            const email = document.getElementById('buscarCliente').value.trim();
            const estado = document.getElementById('filtroEstado').value;
            const fecha = document.getElementById('filtroFecha').value;
            const perPage = document.getElementById('resultadosPorPagina').value;

            const query = new URLSearchParams({
                id: idPedido,
                email: email,
                estado: estado,
                fecha: fecha,
                perPage: perPage
            });

            fetch(`/admin/pedidos/buscar?${query}`)
                .then(res => {
                    if (res.status === 204) {
                        document.getElementById('pedidosContainer').innerHTML = '';
                        document.getElementById('sinResultados').style.display = 'block';
                        return;
                    }
                    return res.text();
                })
                .then(html => {
                    if (html) {
                        document.getElementById('pedidosContainer').innerHTML = html;
                        document.getElementById('sinResultados').style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Error al buscar:', err);
                    document.getElementById('pedidosContainer').innerHTML = '<div class="alert alert-danger">Ocurrió un error al buscar.</div>';
                });
        });

        document.getElementById('btnLimpiarFiltros').addEventListener('click', () => {
            document.getElementById('buscarIdPedido').value = '';
            document.getElementById('buscarCliente').value = '';
            document.getElementById('filtroEstado').value = '';
            document.getElementById('filtroFecha').value = '';
            document.getElementById('resultadosPorPagina').value = '10';
            document.getElementById('pedidosContainer').innerHTML = '';
            document.getElementById('sinResultados').style.display = 'none';
        });
    });
</script>




@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pedidosAdmin.css') }}">
@endpush

@push('scripts')
<script>
    function verDetalle(pedidoId) {
        window.location.href = `/admin/pedidos/${pedidoId}`;
    }
</script>
@endpush