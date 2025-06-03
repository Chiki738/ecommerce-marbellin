@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-center display-4 mb-5 fw-bold">
        @if(isset($categoriaSeleccionada))
        Categoría: {{ $categoriaSeleccionada }}
        @else
        Bienvenido a Marbellin
        @endif
    </h2>

    <div class="row">
        @foreach($productos as $producto)
        <div class="col-md-4 mb-4">
            <div class="card shadow" style="width: 18rem;">
                <img src="{{ asset('storage/' . $producto->imagen) }}"
                    class="card-img-top"
                    alt="{{ $producto->nombre }}"
                    style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover;">
                <div class="card-body">
                    <h6 class="card-title fw-bold">{{ $producto->nombre }}</h6>
                    <p class="card-text">
                        <strong>Categoría:</strong> {{ ucwords(str_replace('_', ' ', $producto->categoria)) }}<br>
                        <strong>Precio:</strong> S/ {{ number_format($producto->precio, 2) }}
                    </p>
                    <a href="#" class="btn btn-warning">Ver producto</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection