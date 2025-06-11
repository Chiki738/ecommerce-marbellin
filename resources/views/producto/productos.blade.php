@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5" style="min-height: 100vh;">
    <div class="row">
        @foreach($productos as $producto)
        <div class="col-md-4 mb-4">
            <div class="card shadow" style="width: 18rem;">
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
        </div>
        @endforeach
    </div>
</div>
@endsection