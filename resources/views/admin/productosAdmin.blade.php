@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">
                <i class="bi bi-box text-primary me-2"></i>Administración de Productos
            </h1>
            <p class="text-muted">Gestión de inventario y variantes</p>
        </div>
        <a href="#" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
            <i class="bi bi-plus-lg me-1"></i> Agregar Producto
        </a>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row mb-4">
        @php
        $stockCritico = $variantes->where('cantidad', '<=', 5);
            $stockBajo=$variantes->where('cantidad', '<=', 13);
                @endphp

                <div class="col-md-6">
                <div class="card shadow-sm text-center bg-warning">
                    <div class="card-body">
                        <h6 class="text-dark">Stock Bajo (&le;13)</h6>
                        <h3 class="text-dark stock-bajo-count">{{ $stockBajo->count() }}</h3>
                    </div>
                </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm text-center bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white">Stock Crítico (&le;5)</h6>
                <h3 class="stock-critico-count">{{ $stockCritico->count() }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- Alerta de stock agrupada --}}
@php
$alertasCritico = $stockCritico->groupBy('producto_codigo');
$alertasBajo = $stockBajo->reject(fn($v) => $stockCritico->contains('producto_codigo', $v->producto_codigo))
->groupBy('producto_codigo');
@endphp

@if ($alertasCritico->isNotEmpty() || $alertasBajo->isNotEmpty())
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>¡Advertencia!</strong> Hay {{ $alertasCritico->count() }} productos con variantes en stock crítico y {{ $alertasBajo->count() }} productos con variantes en stock bajo.
    <ul class="mb-0 stock-alert-list">
        @foreach ($alertasCritico as $codigo => $grupo)
        <li>{{ $productos->firstWhere('codigo', $codigo)->nombre }} (stock crítico)</li>
        @endforeach
        @foreach ($alertasBajo as $codigo => $grupo)
        <li>{{ $productos->firstWhere('codigo', $codigo)->nombre }} (stock bajo)</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Búsqueda --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input id="buscarProducto" type="text" class="form-control" placeholder="Buscar por nombre o código">
        </div>
    </div>
</div>

{{-- Productos con variantes --}}
<div class="accordion" id="productosAccordion">
    @foreach ($productos as $producto)
    <div class="accordion-item mb-3 producto-item" data-codigo="{{ strtolower($producto->codigo) }}" data-nombre="{{ strtolower($producto->nombre) }}">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-{{ $loop->index }}">
                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="img-thumbnail me-3" style="width:60px;height:60px;object-fit:cover">
                <strong class="me-3">{{ $producto->codigo }} - {{ $producto->nombre }}</strong>
                <span class="text-primary">S/ {{ number_format($producto->precio, 2) }}</span>
            </button>
        </h2>
        <div id="flush-{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#productosAccordion">
            <div class="accordion-body">
                <p><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $producto->descripcion }}</p>
                <div class="mb-3 text-center d-flex justify-content-around">
                    <a href="#" class="btn btn-warning btn-sm btnEditarProducto"
                        data-codigo="{{ $producto->codigo }}"
                        data-nombre="{{ $producto->nombre }}"
                        data-precio="{{ $producto->precio }}"
                        data-categoria="{{ $producto->categoria_id }}"
                        data-descripcion="{{ $producto->descripcion }}"
                        data-imagen="{{ Str::startsWith($producto->imagen, ['http://', 'https://']) ? $producto->imagen : asset('storage/' . $producto->imagen) }}"
                        data-bs-toggle="modal"
                        data-bs-target="#editarProducto">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form class="d-inline" action="{{ route('productos.destroy', $producto->codigo) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btnEliminarProducto"
                            data-action="{{ route('productos.destroy', $producto->codigo) }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </div>

                @php
                $variantesProducto = $variantes->where('producto_codigo', $producto->codigo);
                @endphp
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Cantidad</th>
                            <th>Creado</th>
                            <th>Actualizado</th>
                            F
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variantesProducto as $variante)
                        @php
                        $class = $variante->cantidad <= 5 ? 'table-danger' : ($variante->cantidad <= 13 ? 'table-warning' : 'table-success' );
                                @endphp
                                <tr class="{{ $class }}" data-cantidad="{{ $variante->cantidad }}" data-producto="{{ $producto->codigo }}" data-nombre="{{ $producto->nombre }}">
                                <td>{{ $variante->id }}</td>
                                <td>{{ $variante->talla }}</td>
                                <td>{{ $variante->color }}</td>
                                <td>
                                    <form class="form-actualizar-cantidad d-flex align-items-center gap-2" data-id="{{ $variante->id }}">
                                        @csrf
                                        <input type="number" name="cantidad" min="0" value="{{ $variante->cantidad }}" class="form-control form-control-sm cantidad-input" style="width: 80px;">
                                        <button type="submit" class="btn btn-warning btn-sm actualizar-btn" disabled>Actualizar</button>
                                    </form>
                                </td>
                                <td>{{ $variante->created_at }}</td>
                                <td>{{ $variante->updated_at }}</td>
                                </tr>
                                @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
</div>
@include('modals.editarProducto')
@include('modals.agregarProducto')
@endsection
