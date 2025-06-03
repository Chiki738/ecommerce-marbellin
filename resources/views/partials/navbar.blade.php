<nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color: #e3f2fd; position: relative;" data-bs-theme="light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" class="" alt="Logo" style="height: 80px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar grande -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{ url('/') }}">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categorías
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ url('/categoria/Bikinis') }}">Bikinis</a></li>
                        <li><a class="dropdown-item" href="{{ url('/categoria/Cacheteros') }}">Cacheteros</a></li>
                        <li><a class="dropdown-item" href="{{ url('/categoria/Semi Hilos') }}">Semi Hilos</a></li>
                        <li><a class="dropdown-item" href="{{ url('/categoria/Otros productos') }}">Otros productos</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Filtros
                    </a>
                    <ul class="dropdown-menu p-3" style="min-width: 300px;">
                        <!-- Filtro por Color -->
                        <!-- Filtro por Color -->
                        <li>
                            <strong class="dropdown-header">Color</strong>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Rojo" id="colorRojo">
                                <label class="form-check-label" for="colorRojo">Rojo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Amarillo" id="colorAmarillo">
                                <label class="form-check-label" for="colorAmarillo">Amarillo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Blanco" id="colorBlanco">
                                <label class="form-check-label" for="colorBlanco">Blanco</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Negro" id="colorNegro">
                                <label class="form-check-label" for="colorNegro">Negro</label>
                            </div>
                        </li>

                        <hr class="dropdown-divider">

                        <!-- Filtro por Talla -->
                        <li>
                            <strong class="dropdown-header">Talla</strong>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="S" id="tallaS">
                                <label class="form-check-label" for="tallaS">S</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="M" id="tallaM">
                                <label class="form-check-label" for="tallaM">M</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="L" id="tallaL">
                                <label class="form-check-label" for="tallaL">L</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="XL" id="tallaXL">
                                <label class="form-check-label" for="tallaXL">XL</label>
                            </div>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Carrito</a>
                </li>
            </ul>

            <form class="d-flex" role="search" onsubmit="event.preventDefault(); /* agregar lógica aquí */">
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

        <!-- Navbar móvil -->
        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MARBELLIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categorías
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Bikinis</a></li>
                            <li><a class="dropdown-item" href="#">Cacheteros</a></li>
                            <li><a class="dropdown-item" href="#">Semi Hilos</a></li>
                            <li><a class="dropdown-item" href="#">Otros productos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Carrito</a>
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