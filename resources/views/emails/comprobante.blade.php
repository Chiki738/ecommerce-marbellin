<h2>Gracias por tu compra</h2>
<p>Detalles de tu pedido <strong>#{{ $pedido->id }}</strong></p>

@php
$productosAgrupados = [];
foreach ($pedido->detalles as $detalle) {
$nombre = $detalle->producto->nombre;
$productosAgrupados[$nombre][] = $detalle;
}
@endphp

@foreach ($productosAgrupados as $nombreProducto => $detalles)
<h4>{{ $nombreProducto }}</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
    <thead>
        <tr>
            <th style="border: 1px solid #ccc; padding: 6px;">Color</th>
            <th style="border: 1px solid #ccc; padding: 6px;">Talla</th>
            <th style="border: 1px solid #ccc; padding: 6px;">SKU</th>
            <th style="border: 1px solid #ccc; padding: 6px;">Cantidad</th>
            <th style="border: 1px solid #ccc; padding: 6px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detalles as $detalle)
        <tr>
            <td style="border: 1px solid #ccc; padding: 6px;">{{ $detalle->variante->color }}</td>
            <td style="border: 1px solid #ccc; padding: 6px;">{{ $detalle->variante->talla }}</td>
            <td style="border: 1px solid #ccc; padding: 6px;">{{ $detalle->variante->sku }}</td>
            <td style="border: 1px solid #ccc; padding: 6px;">{{ $detalle->cantidad }}</td>
            <td style="border: 1px solid #ccc; padding: 6px;">S/ {{ number_format($detalle->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach

<p><strong>Total del Pedido: S/ {{ number_format($pedido->total, 2) }}</strong></p>

<hr>

<h3><strong>¡IMPORTANTE!</strong></h3>
<p>Solo se permiten <strong>cambios por errores en el envío o productos defectuosos</strong>, dentro de los <strong>2 días hábiles</strong> después de la entrega.</p>
<p><strong>No se aceptan devoluciones</strong> por ningún motivo una vez vencido ese plazo.</p>