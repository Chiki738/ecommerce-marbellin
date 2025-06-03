<nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color: #e3f2fd; position: relative;" data-bs-theme="light">
    <div class="container-fluid">
        <h1 class="display-6 fw-bold me-3">Administrador</h1>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar grande -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.productosAdmin') }}">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboardAdmin') }}">Dashboard</a>
                </li>

            </ul>

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
</nav>