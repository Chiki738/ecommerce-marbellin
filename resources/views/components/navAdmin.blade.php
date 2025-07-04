<nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color: #e3f2fd; position: relative;" data-bs-theme="light">
    <div class="container-fluid">
        <h1 class="display-6 fw-bold me-3">Administrador</h1>

        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/productos') ? 'fw-bold' : '' }}" href="{{ route('admin.productosAdmin') }}">
                        <i class="bi bi-box me-1"></i>Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/dashboard') ? 'fw-bold' : '' }}" href="{{ route('admin.dashboardAdmin') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/pedidos') ? 'fw-bold' : '' }}" href="{{ route('admin.pedidosAdmin') }}">
                        <i class="bi bi-list-check me-1"></i>Pedidos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/clientes') ? 'fw-bold' : '' }}" href="{{ route('admin.dashboardAdmin') }}">
                        <i class="bi bi-people me-1"></i>Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/reclamos') ? 'fw-bold' : '' }}" href="{{ route('admin.dashboardAdmin') }}">
                        <i class="bi bi-exclamation-triangle me-1"></i>Reclamos
                    </a>
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