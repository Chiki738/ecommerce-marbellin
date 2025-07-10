<nav class="navbar navbar-expand-lg bg-light position-relative" data-bs-theme="light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="navbar-logo">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar grande -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @php
                $navItems = [
                ['name' => 'Inicio', 'url' => url('/'), 'icon' => 'fa-house', 'active' => request()->is('/')],
                ['name' => 'Catálogo', 'url' => route('productos.vista'), 'icon' => 'fa-bag-shopping', 'active' => request()->is('productos')],
                ];
                if (auth()->check()) {
                $navItems[] = ['name' => 'Carrito', 'url' => route('carrito'), 'icon' => 'fa-cart-shopping', 'active' => request()->is('carrito')];
                $navItems[] = ['name' => 'Historial', 'url' => route('client.historial'), 'icon' => 'fa-clock-rotate-left', 'active' => request()->is('historial')];
                }
                @endphp

                @foreach($navItems as $item)
                <li class="nav-item">
                    <a class="nav-link {{ $item['active'] ? 'text-black' : 'text-secondary' }}" href="{{ $item['url'] }}">
                        <i class="fa-solid {{ $item['icon'] }}"></i>&nbsp;{{ $item['name'] }}
                        @if($item['name'] === 'Carrito') @endif
                    </a>
                </li>
                @endforeach
            </ul>

            <!-- Buscador -->
            <form class="d-flex buscador-autocompletado position-relative" role="search" onsubmit="event.preventDefault();">
                <input id="buscador-input" class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar" autocomplete="off">
                <div id="resultados-autocompletado"></div>
            </form>

            <!-- Login / Logout -->
            @guest
            <a href="{{ url('/acceso') }}" class="btn btn-outline-primary ms-3">Login / Signup</a>
            @else
            <form method="POST" action="{{ route('logout') }}" class="d-inline ms-3">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
            </form>
            @endguest
        </div>

        <!-- Navbar móvil -->
        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MARBELLIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    @foreach($navItems as $item)
                    <li class="nav-item">
                        <a class="nav-link {{ $item['active'] ? 'text-black' : 'text-secondary' }}" href="{{ $item['url'] }}">
                            <i class="fa-solid {{ $item['icon'] }}"></i>&nbsp;{{ $item['name'] }}
                            @if($item['name'] === 'Carrito')
                            <span class="badge bg-danger">{{ session('carrito') ? count(session('carrito')) : 0 }}</span>
                            @endif
                        </a>
                    </li>
                    @endforeach
                </ul>

                <!-- Buscador móvil -->
                <form class="d-flex mt-3 mb-3" role="search" onsubmit="event.preventDefault();">
                    <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>

                <!-- Login / Logout -->
                @guest
                <a href="{{ url('/acceso') }}" class="btn btn-outline-primary ms-3">Login / Signup</a>
                @else
                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
                </form>
                @endguest
            </div>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="{{ asset('css/nav.css') }}">
<script src="{{ asset('js/buscador.js') }}"></script>