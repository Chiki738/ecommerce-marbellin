<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Estado del Pedido</title>
</head>

<body>
    <h2>Hola {{ $pedido->cliente->nombre }},</h2>
    <p>Tu pedido <strong>#{{ $pedido->id }}</strong> ha sido actualizado al estado:</p>
    <p><strong>{{ ucfirst($estado) }}</strong></p>
    <p>Gracias por tu compra.</p>
</body>

</html>