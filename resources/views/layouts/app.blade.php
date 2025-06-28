<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Marbellin</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script
        src="https://kit.fontawesome.com/e424d20747.js"
        crossorigin="anonymous"></script>
</head>

<body class="activar-spinner">
    {{-- Mostrar navbar solo si no estamos en rutas específicas --}}
    @if (!Request::is('acceso') && !Request::is('acceso/login') && !Request::is('acceso/signup'))
    @include('components.navbar')
    @endif

    @yield('content')

    @include('components.footer')
    @include('sweetalert::alert')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>