@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Filtros laterales -->
        <aside class="col-lg-3 mb-4">
            @include('partials.filtros')
        </aside>

        <!-- Catálogo de productos -->
        <section class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Productos ({{ $productos->total() }})</h4>
            </div>

            <div class="row g-4">
                @foreach($productos as $producto)
                @php
                $categoria = $producto->categoria->nombre ?? 'Sin categoría';
                $imagen = asset($producto->imagen);
                $precio = number_format($producto->precio, 2);
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="text-center" style="height: 250px; overflow: hidden;">
                            <img src="{{ $imagen }}" alt="{{ $producto->nombre }}" class="card-img-top img-fluid" style="max-height: 100%; width: auto;">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $producto->nombre }}</h6>
                            <p class="text-muted small">{{ $categoria }}</p>
                            <span class="h5 text-primary">S/ {{ $precio }}</span>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('producto.detalle', $producto->codigo) }}" class="btn btn-primary w-100">Ver Detalles</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $productos->appends(request()->query())->onEachSide(1)->links('vendor.pagination.bootstrap-5-es') }}
            </div>
        </section>
    </div>
</div>
@endsection