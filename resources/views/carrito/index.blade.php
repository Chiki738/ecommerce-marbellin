@extends('layouts.app')

@section('content')
<main class="container mt-5">
    <h1 class="mb-4 text-primary">🛒 Carrito de Compras</h1>

    @if ($pedido && $pedido->detalles->count())
    @php
    $grupos = $pedido->detalles->groupBy('producto_codigo');
    @endphp

    <ul class="list-group mb-4">
        @foreach ($grupos as $productoCodigo => $detalles)
        @php
        $producto = $detalles->first()->producto;
        @endphp
        <li class="list-group-item shadow-sm rounded mb-3">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="{{ Str::startsWith($producto->imagen, ['http://','https://']) ? $producto->imagen : asset('storage/'.$producto->imagen) }}"
                        alt="{{ $producto->nombre }}"
                        class="img-fluid rounded border"
                        style="width: 150px; height: 150px; object-fit: cover;">

                </div>
                <div class="col-md-9">
                    <h5 class="mb-2">{{ $producto->nombre }}</h5>
                    <p class="text-muted mb-2">Precio unitario: <strong class="text-success">S/ {{ number_format($detalles->first()->precio_unit, 2) }}</strong></p>

                    @foreach ($detalles as $detalle)
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="badge bg-dark">Color: {{ $detalle->color }}</span>
                                <span class="badge bg-secondary">Talla: {{ $detalle->talla }}</span>
                            </div>
                            <div>
                                <form action="{{ route('carrito.actualizar', $detalle->id) }}" method="POST" class="d-inline-flex align-items-center me-2">
                                    @csrf @method('PUT')
                                    <input type="number" name="cantidad" value="{{ $detalle->cantidad }}" min="1"
                                        class="form-control form-control-sm me-1" style="width: 70px;">
                                    <button class="btn btn-sm btn-outline-secondary">Actualizar</button>
                                </form>
                                <form action="{{ route('carrito.eliminar', $detalle->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </div>
                        </div>
                        <small class="text-muted">Subtotal: S/ {{ number_format($detalle->subtotal, 2) }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </li>
        @endforeach
    </ul>

    <div class="text-end mb-5">
        <h4 class="text-dark">🧾 Total: <span class="text-success">S/ {{ number_format($pedido->total, 2) }}</span></h4>
        <form action="{{ route('carrito.checkout') }}" method="POST">
            @csrf
            <button class="btn btn-lg btn-success">Finalizar compra</button>
        </form>
    </div>

    @else
    <div class="alert alert-info">
        Tu carrito está vacío. ¡Agrega productos para comenzar tu compra!
    </div>
    @endif
</main>
@endsection