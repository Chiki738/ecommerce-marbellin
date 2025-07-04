@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="container py-5" style="min-height: 100vh;">
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
                                        <small class="text-muted d-block">
                                            Color: {{ $detalle->variante->color ?? 'No disponible' }} |
                                            Talla: {{ $detalle->variante->talla ?? 'No disponible' }}
                                        </small>

                                        <small class="text-muted d-block">
                                            SKU: {{ $producto->codigo }}-{{ strtoupper($detalle->variante->color ?? 'ND') }}-{{ strtoupper($detalle->variante->talla ?? 'ND') }}
                                        </small>

                                        <small class="text-muted d-block">
                                            Cantidad: <span class="cantidad-texto">{{ $detalle->cantidad }}</span>
                                        </small>

                                        <small class="text-muted d-block">
                                            Subtotal: S/ <span class="subtotal-texto">{{ number_format($detalle->subtotal, 2) }}</span>
                                        </small>

                                    </div>
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('carrito.actualizar', $detalle->id) }}" class="me-2 d-flex form-actualizar" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="cantidad" value="{{ $detalle->cantidad }}" min="1" class="form-control form-control-sm text-center me-1" style="width: 60px;">
                                            <button class="btn btn-outline-secondary btn-sm">Actualizar</button>
                                        </form>

                                        <form action="{{ route('carrito.eliminar', $detalle->id) }}" class="form-eliminar" method="POST">
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
                    <ul id="resumen-productos" class="list-unstyled mb-3">
                        @foreach ($pedido->detalles->groupBy('producto.nombre') as $nombre => $items)
                        <li class="d-flex justify-content-between resumen-item" data-producto="{{ $nombre }}">
                            <span>{{ $nombre }} (<span class="resumen-cantidad">{{ $items->sum('cantidad') }}</span>)</span>
                            <span>S/ <span class="resumen-subtotal">{{ number_format($items->sum('subtotal'), 2) }}</span></span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-primary">S/ <span id="total-pedido">{{ number_format($pedido?->total ?? 0, 2) }}</span></strong>
                    </div>

                    @if ($pedido && $pedido->detalles->count())
                    <input type="hidden" id="pedidoId" value="{{ $pedido->id }}">
                    <div class="d-grid gap-2">
                        <div id="paypal-button-container" class="mt-3"></div>

                        <form action="{{ route('carrito.vaciar') }}" class="form-vaciar">
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

<script src="https://www.paypal.com/sdk/js?client-id=ASdq36S1LmT-GX6If7Pbd7pRsdRtNaSuhsFkFe5BhHhn_nUlrr8KakgZZN057NBnbmM7QYmLJga6LH3R&currency=USD"></script>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const pedidoId = document.getElementById("pedidoId")?.value;

        if (pedidoId) {
            paypal.Buttons({
                createOrder: (data, actions) => {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: document.getElementById('total-pedido').textContent.replace(',', '')
                            }

                        }]
                    });
                },
                onApprove: async (data, actions) => {
                    const resStock = await fetch(`/carrito/verificar-stock/${pedidoId}`, {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    });
                    const stockData = await resStock.json();

                    if (!stockData.success) {
                        Swal.fire({
                            icon: "error",
                            html: stockData.errores.join("<br>"),
                            confirmButtonText: "Aceptar"
                        });
                        return;
                    }

                    await actions.order.capture();
                    try {
                        const res = await fetch(`/pago/exito?pedido_id=${pedidoId}`, {
                            method: "GET",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        });
                        const dataJson = await res.json();
                        Swal.fire({
                            icon: dataJson.success ? "success" : "error",
                            text: dataJson.success ? "Tu pedido fue generado correctamente." : "Error al actualizar el pedido.",
                            timer: 2500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } catch (error) {
                        console.error("Error en pago:", error);
                        Swal.fire({
                            icon: "error",
                            text: "Error al procesar el pago.",
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                },
                onCancel: () => {
                    Swal.fire({
                        icon: "error",
                        text: "El pago fue cancelado.",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }).render('#paypal-button-container');
        }

        document.querySelectorAll('.form-actualizar').forEach(form => {
            form.addEventListener('submit', async e => {
                e.preventDefault();

                const url = form.action;
                const formData = new FormData(form);
                const cantidad = formData.get("cantidad");

                try {
                    const res = await fetch(url, {
                        method: "POST",
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': formData.get('_token')
                        },
                        body: formData
                    });

                    const data = await res.json();

                    Swal.fire('Actualizado', data.message, 'success');
                    form.closest('.border').querySelector('.cantidad-texto').textContent = cantidad;
                    form.closest('.border').querySelector('.subtotal-texto').textContent = parseFloat(data.subtotal).toFixed(2);
                    document.getElementById('total-pedido').textContent = parseFloat(data.total).toFixed(2);

                    const resumenItem = [...document.querySelectorAll('.resumen-item')].find(item => item.dataset.producto === data.producto);
                    if (resumenItem) {
                        resumenItem.querySelector('.resumen-cantidad').textContent = data.resumenCantidad;
                        resumenItem.querySelector('.resumen-subtotal').textContent = parseFloat(data.resumenSubtotal).toFixed(2);
                    }

                } catch (error) {
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });
        });
    });
</script>
@endpush