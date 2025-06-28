<nav class="navbar navbar-expand-lg bg-light" style="background-color: #e3f2fd; position: relative;" data-bs-theme="light">
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
                {{-- Inicio --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'text-black' : 'text-secondary' }}" href="{{ url('/') }}">
                        <i class="fa-solid fa-house"></i>&nbsp;Inicio
                    </a>
                </li>

                {{-- Productos --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('productos') ? 'text-black' : 'text-secondary' }}" href="{{ route('productos.vista') }}">
                        <i class="fa-solid fa-bag-shopping"></i>&nbsp;Catálogo
                    </a>
                </li>

                {{-- Carrito solo si está logueado --}}
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('carrito') ? 'text-black' : 'text-secondary' }}" href="{{ route('carrito') }}">
                        <i class="fa-solid fa-cart-shopping"></i>&nbsp;Carrito </a>
                </li>
                @endauth
            </ul>

            {{-- Buscador --}}
            <form class="d-flex buscador-autocompletado position-relative" role="search" onsubmit="event.preventDefault();">
                <input id="buscador-input" class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar" autocomplete="off">
                <div id="resultados-autocompletado"></div>
            </form>

            {{-- Login/Logout --}}
            @guest
            <a href="{{ url('/acceso') }}" class="btn btn-outline-primary ms-3">Login / Signup</a>
            @endguest

            @auth
            <form method="POST" action="{{ route('logout') }}" class="d-inline ms-3">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
            </form>
            @endauth
        </div>

        <!-- Navbar móvil -->
        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MARBELLIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'text-black' : 'text-secondary' }}" href="{{ url('/') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('productos') ? 'text-black' : 'text-secondary' }}" href="{{ route('productos.vista') }}">Catálogo</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a href="{{ route('carrito') }}" class="nav-link {{ request()->is('carrito') ? 'text-black' : 'text-secondary' }}">
                            Carrito <span class="badge bg-danger">{{ session('carrito') ? count(session('carrito')) : 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('carrito') }}" class="nav-link {{ request()->is('carrito') ? 'text-black' : 'text-secondary' }}">
                             <span class="badge bg-danger">{{ session('carrito') ? count(session('carrito')) : 0 }}</span>
                        </a>
                    </li>
                    @endauth
                </ul>

                <form class="d-flex mt-3 mb-3" role="search" onsubmit="event.preventDefault();">
                    <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>

                @guest
                <a href="{{ url('/acceso') }}" class="btn btn-outline-primary ms-3">Login / Signup</a>
                @endguest

                @auth
                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
                </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="{{ asset('css/nav.css') }}">
<script src="{{ asset('js/buscador.js') }}"></script>