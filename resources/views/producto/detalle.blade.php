@extends('layouts.app')

@section('content')
@php
$imagenProducto = Str::startsWith($producto->imagen, ['http://', 'https://'])
    ? $producto->imagen
    : asset($producto->imagen);
$logueado = auth()->check();
$tieneStock = $producto->variantes->sum('cantidad') > 0;
@endphp

<div class="container mt-4 py-5">
    <div class="row g-5 align-items-start">
        <div class="col-md-6 d-flex justify-content-center">
            <div class="imagen-wrapper">
                <img src="{{ $imagenProducto }}" alt="{{ $producto->nombre }}">
            </div>
        </div>

        <div class="col-md-6">
            <span class="badge bg-primary mb-3">{{ optional($producto->categoria)->nombre ?? 'Sin categoría' }}</span>
            <h1 class="fw-bold h2">{{ $producto->nombre }}</h1>

            <p class="text-muted">
                Código {{ $producto->codigo }}
            </p>

            <p class="lead">{{ $producto->descripcion }}</p>

            <div class="mb-3">
                <span class="h4 text-primary">S/ {{ number_format($producto->precio, 2) }}</span>
            </div>

            <form id="formAgregarCarrito" method="POST" action="{{ route('carrito.agregar') }}">
                @csrf
                <input type="hidden" name="producto_codigo" value="{{ $producto->codigo }}">

                <div class="mb-3">
                    <h6 class="fw-bold">
                        <i class="bi bi-arrows-fullscreen"></i> Talla:
                    </h6>
                    <div class="btn-group" role="group">
                        @foreach($producto->variantes->pluck('talla')->unique() as $talla)
                        <input type="radio" class="btn-check" name="talla" id="talla_{{ $talla }}" value="{{ $talla }}"
                            {{ $producto->variantes->where('talla', $talla)->sum('cantidad') == 0 ? 'disabled' : '' }}>
                        <label class="btn btn-outline-primary" for="talla_{{ $talla }}">{{ $talla }}</label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">
                        <i class="bi bi-palette"></i> Color:
                    </h6>
                    <div class="colores-container">
                        @foreach($producto->variantes->pluck('color')->unique() as $color)
                        @php
                        $colorId = strtolower(preg_replace('/\s+/', '_', $color));
                        @endphp
                        <input type="radio" name="color" id="color_{{ $colorId }}" value="{{ $color }}"
                            {{ $producto->variantes->where('color', $color)->sum('cantidad') == 0 ? 'disabled' : '' }}>
                        <label for="color_{{ $colorId }}" class="color-label color-{{ $colorId }}" title="{{ ucfirst($color) }}"></label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">
                        <i class="fa-solid fa-hashtag"></i> Cantidad:
                    </h6>
                    <input type="number" name="cantidad" id="cantidad" value="1" min="1" class="form-control" style="width: 100px;">
                </div>

                <button type="submit" class="btn btn-success mt-3 px-4"
                    @disabled(!$logueado || !$tieneStock)
                    title="{{ !$logueado ? 'Debes iniciar sesión para comprar' : (!$tieneStock ? 'Producto sin stock disponible' : '') }}">
                    @if(!$logueado)
                    Iniciar sesión para comprar
                    @elseif(!$tieneStock)
                    Sin stock disponible
                    @else
                    Agregar al carrito
                    @endif
                </button>
            </form>
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
