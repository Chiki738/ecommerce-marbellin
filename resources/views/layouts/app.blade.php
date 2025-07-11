<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Marbellin</title>

    {{-- Favicon e iconos --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/e424d20747.js" crossorigin="anonymous"></script>

    {{-- Estilo base del sitio --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    {{-- Estilos adicionales (por página) --}}
    @stack('styles')
</head>

<body>
    {{-- Navbar condicional --}}
    @switch(true)
    @case(Request::is('admin*'))
    @include('components.navAdmin')
    @break

    @case(!Request::is('acceso*'))
    @include('components.navbar')
    @break
    @endswitch

    {{-- Contenido principal --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer visible solo fuera de rutas admin y acceso --}}
    @unless (Request::is('admin*') || Request::is('acceso*'))
    @include('components.footer')
    @endunless

    {{-- Alertas con SweetAlert2 --}}
    @include('sweetalert::alert')

    {{-- Scripts base --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Scripts adicionales (por página) --}}
    @stack('scripts')
</body>

</html>