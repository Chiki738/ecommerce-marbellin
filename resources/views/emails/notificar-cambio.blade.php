<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estado de tu solicitud</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
    <h2 style="color: #007bff;">Hola {{ $cambio->pedido->cliente->nombre ?? 'cliente' }},</h2>

    <p>{{ $mensaje }}</p>

    <p>Si tienes dudas o necesitas más ayuda, puedes visitarnos en tienda o escribirnos.</p>

    <hr style="border: none; border-top: 1px solid #ccc;">
    <p style="font-size: 12px; color: #777;">
        Gracias por confiar en nosotros.<br>
        <strong>Marbellin E-Commerce</strong>
    </p>
</body>

</html>