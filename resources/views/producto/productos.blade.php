@extends('layouts.app')

@section('content')
<style>
    @media (min-width: 980px) {
        .justify-md-evenly {
            justify-content: space-evenly !important;
        }
    }
</style>

<div class="mt-4 mb-5 px-1" style="min-height: 100vh;">
    <div class="d-flex flex-wrap gap-3 justify-content-center justify-md-evenly">
        @foreach($productos as $producto)
        <div class="card shadow mb-sm-3" style="width: 18rem;">
            <img src="{{ asset($producto->imagen) }}"
                class="card-img-top"
                alt="{{ $producto->nombre }}"
                style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover;">
            <div class="card-body">
                <h6 class="card-title fw-bold">{{ $producto->nombre }}</h6>
                <p class="card-text">
                    <strong>Categoría:</strong> {{ $producto->categoria ? ucwords($producto->categoria->nombre) : 'Sin categoría' }} <br>
                    <strong>Precio:</strong> S/ {{ number_format($producto->precio, 2) }}
                </p>
                <a href="{{ route('producto.detalle', $producto->codigo) }}" class="btn btn-warning">Ver producto</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection