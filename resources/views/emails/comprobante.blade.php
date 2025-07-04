<h2>Gracias por tu compra</h2>
<p>Detalles de tu pedido #{{ $pedido->id }}</p>

<ul>
    @foreach($pedido->detalles as $detalle)
    <li>
        {{ $detalle->producto->nombre }} ({{ $detalle->cantidad }}) - S/ {{ number_format($detalle->subtotal, 2) }}
    </li>
    @endforeach
</ul>

<p><strong>Total: S/ {{ number_format($pedido->total, 2) }}</strong></p>