@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-cart3"></i> Mi Carrito</h2>
                <a href="{{ route('productos.vista') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Seguir Comprando
                </a>
            </div>

            @if ($pedido && $pedido->detalles->count())
            @php $grupos = $pedido->detalles->groupBy('producto_codigo'); @endphp

            @foreach ($grupos as $productoCodigo => $detalles)
            @php $producto = $detalles->first()->producto; @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="{{ Str::startsWith($producto->imagen, ['http://','https://']) ? $producto->imagen : asset('storage/'.$producto->imagen) }}" class="img-fluid rounded" style="aspect-ratio: 1/1; object-fit: cover;" alt="{{ $producto->nombre }}">
                        </div>
                        <div class="col-md-10">
                            <h6 class="mb-2">{{ $producto->nombre }}</h6>
                            @foreach ($detalles as $detalle)
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Color: {{ $detalle->variante->color }} | Talla: {{ $detalle->variante->talla }}</small>
                                        <small class="text-muted d-block">SKU: {{ $producto->codigo }}-{{ strtoupper($detalle->variante->color) }}-{{ strtoupper($detalle->variante->talla) }}</small>
                                        <small class="text-muted d-block">Cantidad: {{ $detalle->cantidad }}</small>
                                        <small class="text-muted d-block">Subtotal: S/ {{ number_format($detalle->subtotal, 2) }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('carrito.actualizar', $detalle->id) }}" method="POST" class="me-2 d-flex">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="cantidad" value="{{ $detalle->cantidad }}" min="1" class="form-control form-control-sm text-center me-1" style="width: 60px;">
                                            <button class="btn btn-outline-secondary btn-sm">Actualizar</button>
                                        </form>
                                        <form action="{{ route('carrito.eliminar', $detalle->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="alert alert-info">
                Tu carrito está vacío. ¡Agrega productos para comenzar tu compra!
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Resumen del Pedido</h5>
                </div>
                <div class="card-body">
                    @if ($pedido && $pedido->detalles->count())
                    @php
                    $resumen = $pedido->detalles->groupBy(function($item) {
                    return $item->producto->nombre;
                    });
                    @endphp
                    <ul class="list-unstyled mb-3">
                        @foreach ($resumen as $nombre => $items)
                        @php
                        $totalCantidad = $items->sum('cantidad');
                        $totalSubtotal = $items->sum('subtotal');
                        @endphp
                        <li class="d-flex justify-content-between">
                            <span>{{ $nombre }} ({{ $totalCantidad }})</span>
                            <span>S/ {{ number_format($totalSubtotal, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-primary">S/ {{ number_format($pedido?->total ?? 0, 2) }}</strong>
                    </div>

                    @if ($pedido && $pedido->detalles->count())
                    <input type="hidden" id="pedidoId" value="{{ $pedido->id }}">
                    <div class="d-grid gap-2">
                        <div id="paypal-button-container" class="mt-3"></div>

                        <form action="{{ route('carrito.vaciar') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-secondary w-100" type="submit">
                                <i class="bi bi-trash"></i> Vaciar Carrito
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SDK de PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=ASdq36S1LmT-GX6If7Pbd7pRsdRtNaSuhsFkFe5BhHhn_nUlrr8KakgZZN057NBnbmM7QYmLJga6LH3R&currency=USD"></script>

<script>
    const pedidoId = document.getElementById('pedidoId')?.value;

    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '{{ number_format($pedido?->total ?? 0, 2, ".", "") }}'
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                fetch(`/pago/exito?pedido_id=${pedidoId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ Tu pedido fue generado correctamente.');
                            window.location.href = '/carrito'; // o redirige a productos o dashboard
                        } else {
                            alert('Error al actualizar el pedido en el sistema.');
                        }
                    });
            });
        },
        onCancel: function(data) {
            window.location.href = "/pago/cancelado";
        },
    }).render('#paypal-button-container');
</script>
@endsection