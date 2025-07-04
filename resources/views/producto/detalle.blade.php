@extends('layouts.app')

@section('content')
<div class="container mt-4 py-5">
    <div class="row">
        <!-- Imagen -->
        <div class="col-md-6 d-flex justify-content-center">
            <div class="imagen-wrapper">
                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}">
            </div>
        </div>

        <!-- Info producto -->
        <div class="col-md-6">
            <h2 class="fw-bold">{{ $producto->nombre }}</h2>

            <p class="text-muted">
                <i class="bi bi-tag"></i>
                <strong>Categoría:</strong> {{ $producto->categoria->nombre }}
            </p>

            <p>
                <i class="bi bi-card-text"></i>
                {{ $producto->descripcion }}
            </p>

            <div class="mb-3"> <span class="h4 text-primary">S/ {{ number_format($producto->precio, 2) }}</span>
            </div>

            <form id="formAgregarCarrito" method="POST" action="{{ route('carrito.agregar') }}">
                @csrf
                <input type="hidden" name="producto_codigo" value="{{ $producto->codigo }}">

                <!-- Talla -->
                <div class="mb-3">
                    <h6 class="fw-bold">
                        <i class="bi bi-arrows-fullscreen"></i> Talla:
                    </h6>
                    <div class="btn-group" role="group">
                        @foreach($producto->variantes->pluck('talla')->unique() as $talla)
                        @php $stock = $producto->variantes->where('talla', $talla)->sum('cantidad'); @endphp
                        <input type="radio" class="btn-check" name="talla" id="talla_{{ $talla }}" value="{{ $talla }}" {{ $stock == 0 ? 'disabled' : '' }}>
                        <label class="btn btn-outline-primary" for="talla_{{ $talla }}">{{ $talla }}</label>
                        @endforeach
                    </div>
                </div>

                <!-- Color -->
                <div class="mb-3">
                    <h6 class="fw-bold">
                        <i class="bi bi-palette"></i> Color:
                    </h6>
                    <div class="colores-container">
                        @foreach($producto->variantes->pluck('color')->unique() as $color)
                        @php
                        $cantidadColor = $producto->variantes->where('color', $color)->sum('cantidad');
                        $colorId = strtolower(preg_replace('/\s+/', '_', $color));
                        $colorClass = 'color-' . $colorId;
                        @endphp
                        <input type="radio" name="color" id="color_{{ $colorId }}" value="{{ $color }}" {{ $cantidadColor == 0 ? 'disabled' : '' }}>
                        <label for="color_{{ $colorId }}" class="color-label {{ $colorClass }}" title="{{ ucfirst($color) }}"></label>
                        @endforeach
                    </div>
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <h6 for="cantidad" class="fw-bold">
                        <i class="fa-solid fa-hashtag"></i> Cantidad:
                    </h6>
                    <input type="number" name="cantidad" id="cantidad" value="1" min="1" class="form-control" style="width: 100px;">
                </div>

                <!-- Botón -->
                <button type="submit" class="btn btn-success mt-3" {{ auth()->check() ? '' : 'disabled title=Debes iniciar sesión para comprar' }}>
                    {{ auth()->check() ? 'Agregar al carrito' : 'Iniciar sesión para comprar' }}
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast éxito -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11000;">
    <div id="successToast" class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="successToastMsg">Producto agregado al carrito</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/producto-detalle.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/producto-detalle.js') }}"></script>
@endpush