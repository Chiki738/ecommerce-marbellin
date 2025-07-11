<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $pedido->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            margin: 10px 0 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>

    <h2>Información del Cliente</h2>
    <p><strong>Nombre:</strong> {{ $pedido->cliente->nombre }}</p>
    <p><strong>Email:</strong> {{ $pedido->cliente->email }}</p>
    <p><strong>Distrito:</strong> {{ $pedido->cliente->distrito->nombre ?? 'No disponible' }}</p>
    <p><strong>Provincia:</strong> {{ $pedido->cliente->distrito->provincia->nombre ?? 'No disponible' }}</p>
    <p><strong>Cliente desde:</strong> {{ $pedido->cliente->created_at->format('d/m/Y') }}</p>

    <h2>Dirección de Envío</h2>
    <p>{{ $pedido->direccion_envio }}</p>

    <h2>Productos del Pedido</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Variante</th>
                <th>Precio Unit.</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->codigo }}</td>
                <td>{{ $detalle->producto->nombre }}</td>
                <td>Talla {{ $detalle->variante->talla }} / {{ $detalle->variante->color }}</td>
                <td>S/ {{ number_format($detalle->precio_unit, 2) }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right"><strong>Total:</strong></td>
                <td><strong>S/ {{ number_format($pedido->total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

</body>

</html>