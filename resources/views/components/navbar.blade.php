<nav class="navbar navbar-expand-lg bg-light" style="background-color: #e3f2fd; position: relative;" data-bs-theme="light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="navbar-logo">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
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
                        <i class="fa-solid fa-bag-shopping"></i>&nbsp;Productos
                    </a>
                </li>

                {{-- Filtros --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-black" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" tabindex="0">
                        <i class="fa-solid fa-filter"></i>&nbsp;Filtros
                    </a>

                    <form method="GET" action="{{ route('productos.filtrar') }}" class="dropdown-menu p-3" style="min-width: 250px;" id="filtrosForm">
                        <div class="d-flex flex-column">

                            {{-- Categoría --}}
                            <div class="mb-3">
                                <strong class="dropdown-header">Categoría</strong>
                                @php $categorias = \App\Models\Categoria::pluck('nombre', 'categoria_id'); @endphp
                                @foreach($categorias as $id => $nombre)
                                <div class="form-check">
                                    <input class="form-check-input filtro-checkbox" type="checkbox" name="categorias[]" value="{{ $id }}" id="categoria{{ $loop->index }}"
                                        {{ in_array($id, request('categorias', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="categoria{{ $loop->index }}">{{ ucwords(str_replace('_', ' ', $nombre)) }}</label>
                                </div>
                                @endforeach

                            </div>

                            {{-- Color --}}
                            <div class="mb-3">
                                <strong class="dropdown-header">Color</strong>
                                @php
                                $colores = \App\Models\VarianteProducto::select('color')->distinct()->pluck('color');
                                @endphp
                                @foreach($colores as $color)
                                <div class="form-check">
                                    <input class="form-check-input filtro-checkbox" type="checkbox" name="colores[]" value="{{ $color }}" id="color{{ $color }}"
                                        {{ in_array($color, request('colores', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="color{{ $color }}">{{ $color }}</label>
                                </div>
                                @endforeach
                            </div>

                            {{-- Talla --}}
                            <div class="mb-3">
                                <strong class="dropdown-header">Talla</strong>
                                @php
                                $tallas = \App\Models\VarianteProducto::select('talla')->distinct()->pluck('talla');
                                @endphp
                                @foreach($tallas as $talla)
                                <div class="form-check">
                                    <input class="form-check-input filtro-checkbox" type="checkbox" name="tallas[]" value="{{ $talla }}" id="talla{{ $talla }}"
                                        {{ in_array($talla, request('tallas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="talla{{ $talla }}">{{ $talla }}</label>
                                </div>
                                @endforeach
                            </div>

                            {{-- Botón filtrar --}}
                            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>

                        </div>
                    </form>
                </li>

                {{-- Carrito --}}
                <li class="nav-item">
                    @guest
                    <a href="{{ url('/acceso') }}" class="nav-link text-secondary" title="Debe iniciar sesión para usar el carrito">
                        <i class="fa-solid fa-cart-shopping"></i>&nbsp;Carrito
                    </a>
                    @else
                    <a href="{{ route('carrito') }}" class="nav-link {{ request()->is('carrito') ? 'text-black' : 'text-secondary' }}">
                        <i class="fa-solid fa-cart-shopping"></i>&nbsp;Carrito
                    </a>
                    @endguest
                </li>
            </ul>

            {{-- Buscador --}}
            <form class="d-flex" role="search" onsubmit="event.preventDefault(); /* agregar lógica aquí */">
                <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">
                <button class="btn btn-outline-success" type="submit">Buscar</button>
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
                        <a class="nav-link {{ request()->is('productos') ? 'text-black' : 'text-secondary' }}" href="{{ route('productos.vista') }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        @guest
                        <a href="{{ url('/acceso') }}" class="nav-link text-secondary" title="Debe iniciar sesión para usar el carrito">Carrito</a>
                        @else
                        <a href="{{ route('carrito') }}" class="nav-link {{ request()->is('carrito') ? 'text-black' : 'text-secondary' }}">Carrito</a>
                        @endguest
                    </li>
                </ul>

                <form class="d-flex mt-3 mb-3" role="search" onsubmit="event.preventDefault(); /* agregar lógica aquí */">
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

<!-- Estilos personalizados -->
<style>
    .navbar-logo {
        max-height: 50px;
        width: auto;
        height: auto;
        transition: all 0.3s ease;
    }

    #autocompleteResults {
        max-height: 200px;
        overflow-y: auto;
        background: white;
    }

    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1100;
    }
</style>