<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificación 2FA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }

        h2 {
            color: #2c3e50;
        }

        p {
            margin: 0.5em 0;
        }
    </style>
</head>

<body>
    <p>Hola,</p>
    <p>Tu código de verificación es:</p>
    <h2>{{ $codigo }}</h2>
    <p>Este código expirará en <strong>10 minutos</strong>.</p>
</body>

</html>