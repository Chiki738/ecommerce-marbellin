<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta
        name="description"
        content="Descubre las mejores prendas de lencería en línea. Explora nuestra amplia colección de estilos, y disfruta de calidad y confort exclusivos para todos los gustos." />
    <meta name="keywords" content="Marbellin, lencería, ropa interior, calidad, moda, mujeres, confort" />
    <meta name="author" content="Marbellin" />

    <title>Marbellin</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>

    {{-- Mostrar navbar solo si no estás en las rutas 'acceso', 'acceso/login' o 'acceso/signup' --}}
    @if (!Request::is('acceso') && !Request::is('acceso/login') && !Request::is('acceso/signup'))
    @include('partials.navbar')
    @endif

    {{-- Contenido dinámico de cada vista --}}
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>