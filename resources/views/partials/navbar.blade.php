<nav class="navbar navbar-expand-lg bg-light border border-bottom-1 border-dark" style="background-color: #e3f2fd;" data-bs-theme="light">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Logo -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="navbar-logo">
        </a>

        <!-- Botón para menú móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar grande -->
        <div class="collapse navbar-collapse d-none d-lg-flex flex-grow-1 justify-content-between align-items-center" id="navbarSupportedContent">

            <!-- Menú izquierdo -->
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'text-black' : 'text-secondary' }}" href="{{ url('/') }}">
                        <i class="fa-solid fa-house"></i>&nbsp;Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('productos') ? 'text-black' : 'text-secondary' }}" href="{{ route('productos.vista') }}">
                        <i class="fa-solid fa-bag-shopping"></i>&nbsp;Productos
                    </a>
                </li>

                <li class="nav-item dropdown">
                    @if(request()->is('/') || request()->is('acceso'))
                    <a class="nav-link dropdown-toggle text-secondary disabled" href="#" role="button">
                        <i class="fa-solid fa-filter"></i>&nbsp;Filtros
                    </a>
                    @else
                    <a class="nav-link dropdown-toggle text-black" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" tabindex="0">
                        <i class="fa-solid fa-filter"></i>&nbsp;Filtros
                    </a>

                    <form method="GET" action="{{ route('productos.filtrar') }}" class="dropdown-menu p-3" style="min-width: 250px;" id="filtrosForm">
                        <div class="d-flex flex-column">
                            <!-- Categoría -->
                            <div class="mb-3">
                                <strong class="dropdown-header">Categoría</strong>
                                @php $categorias = \App\Models\Producto::select('categoria')->distinct()->pluck('categoria'); @endphp
                                @foreach($categorias as $categoria)
                                <div class="form-check">
                                    <input class="form-check-input filtro-checkbox" type="checkbox" name="categorias[]" value="{{ $categoria }}" id="categoria{{ $loop->index }}"
                                        {{ in_array($categoria, request('categorias', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="categoria{{ $loop->index }}">{{ ucwords(str_replace('_', ' ', $categoria)) }}</label>
                                </div>
                                @endforeach
                            </div>

                            <!-- Color -->
                            <div class="mb-3">
                                <strong class="dropdown-header">Color</strong>
                                @php $colores = \App\Models\VarianteProducto::select('color')->distinct()->pluck('color'); @endphp
                                @foreach($colores as $color)
                                <div class="form-check">
                                    <input class="form-check-input filtro-checkbox" type="checkbox" name="colores[]" value="{{ $color }}" id="color{{ $color }}"
                                        {{ in_array($color, request('colores', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="color{{ $color }}">{{ $color }}</label>
                                </div>
                                @endforeach
                            </div>

                            <!-- Talla -->
                            <div class="mb-3">
                                <strong class="dropdown-header">Talla</strong>
                                @php $tallas = \App\Models\VarianteProducto::select('talla')->distinct()->pluck('talla'); @endphp
                                @foreach($tallas as $talla)
                                <div class="form-check">
                                    <input class="form-check-input filtro-checkbox" type="checkbox" name="tallas[]" value="{{ $talla }}" id="talla{{ $talla }}"
                                        {{ in_array($talla, request('tallas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="talla{{ $talla }}">{{ $talla }}</label>
                                </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
                        </div>
                    </form>
                    @endif
                </li>

                <li class="nav-item">
                    @guest
                    <a href="{{ url('/acceso') }}" class="nav-link text-secondary">
                        <i class="fa-solid fa-cart-shopping"></i>&nbsp;Carrito
                    </a>
                    @else
                    <a href="{{ route('carrito') }}" class="nav-link {{ request()->is('carrito') ? 'text-black' : 'text-secondary' }}">
                        <i class="fa-solid fa-cart-shopping"></i>&nbsp;Carrito
                    </a>
                    @endguest
                </li>
            </ul>

            <!-- Buscador sin botón -->
            <div class="d-flex align-items-center gap-2 position-relative">
                <form class="d-flex" id="searchForm" onsubmit="return false;" role="search" autocomplete="off">
                    <input id="searchInput" class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar" name="buscar" autocomplete="off" />
                </form>


                <div id="autocompleteResults" class="list-group position-absolute" style="top: 100%; left: 0; right: 0; z-index: 1050;"></div>

                @guest
                <a href="{{ url('/acceso') }}" class="btn btn-outline-primary">Login / Signup</a>
                @endguest

                @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
                </form>
                @endauth
            </div>

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
                        <a href="{{ url('/acceso') }}" class="nav-link text-secondary">Carrito</a>
                        @else
                        <a href="{{ route('carrito') }}" class="nav-link {{ request()->is('carrito') ? 'text-black' : 'text-secondary' }}">
                            @endguest
                    </li>
                </ul>

                @if(!request()->is('/') && !request()->is('acceso'))
                <!-- Filtros en móvil -->
                <form method="GET" action="{{ route('productos.filtrar') }}" class="p-3">
                    <div class="d-flex flex-column">
                        <strong class="dropdown-header">Categoría</strong>
                        @foreach($categorias as $categoria)
                        <div class="form-check">
                            <input class="form-check-input filtro-checkbox" type="checkbox" name="categorias[]" value="{{ $categoria }}" id="movilCategoria{{ $loop->index }}"
                                {{ in_array($categoria, request('categorias', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="movilCategoria{{ $loop->index }}">{{ ucwords(str_replace('_', ' ', $categoria)) }}</label>
                        </div>
                        @endforeach

                        <strong class="dropdown-header mt-3">Color</strong>
                        @foreach($colores as $color)
                        <div class="form-check">
                            <input class="form-check-input filtro-checkbox" type="checkbox" name="colores[]" value="{{ $color }}" id="movilColor{{ $color }}"
                                {{ in_array($color, request('colores', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="movilColor{{ $color }}">{{ $color }}</label>
                        </div>
                        @endforeach

                        <strong class="dropdown-header mt-3">Talla</strong>
                        @foreach($tallas as $talla)
                        <div class="form-check">
                            <input class="form-check-input filtro-checkbox" type="checkbox" name="tallas[]" value="{{ $talla }}" id="movilTalla{{ $talla }}"
                                {{ in_array($talla, request('tallas', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="movilTalla{{ $talla }}">{{ $talla }}</label>
                        </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
                    </div>
                </form>
                @endif

                <form method="GET" action="{{ route('productos.vista') }}" class="d-flex mt-3 mb-3">
                    <input class="form-control me-2" type="text" name="buscar" placeholder="Buscar productos...">
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

<!-- Spinner de carga -->
<div id="loading-overlay" style="display: none;">
    <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const resultsContainer = document.getElementById('autocompleteResults');
        const loadingOverlay = document.getElementById('loading-overlay');

        let debounceTimeout;

        // Autocompletado
        searchInput?.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            const query = searchInput.value.trim();

            if (query.length === 0) {
                resultsContainer.innerHTML = '';
                resultsContainer.style.display = 'none';
                return;
            }

            debounceTimeout = setTimeout(() => {
                fetch(`/productos/autocomplete?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            resultsContainer.innerHTML = '<div class="list-group-item">No se encontraron resultados</div>';
                        } else {
                            resultsContainer.innerHTML = data.slice(0, 5).map(producto => `
                                <a href="/producto/${producto.codigo}" class="list-group-item list-group-item-action d-flex align-items-center sugerencia-link">
                                    <img src="${producto.imagen}" alt="Imagen" style="width: 40px; height: 40px;" class="me-2">
                                    <div>
                                        <strong>${producto.nombre}</strong><br>
                                        <small class="text-muted">Precio: S/ ${producto.precio}</small>
                                    </div>
                                </a>
                            `).join('');
                        }
                        resultsContainer.style.display = 'block';
                    })
                    .catch(() => {
                        resultsContainer.innerHTML = '<div class="list-group-item text-danger">Error al cargar resultados</div>';
                        resultsContainer.style.display = 'block';
                    });
            }, 300);
        });

        // Oculta sugerencias si haces clic fuera
        document.addEventListener('click', (e) => {
            if (!resultsContainer.contains(e.target) && e.target !== searchInput) {
                resultsContainer.style.display = 'none';
            }
        });

        // Redirección manual si se presiona Enter en el input
        document.getElementById('searchForm')?.addEventListener('submit', (e) => {
            e.preventDefault(); // Evita el envío
            // Puedes hacer aquí lo que necesites (por ejemplo, mostrar mensaje)
            // o simplemente no hacer nada como en este caso
        });


        // Mostrar spinner al hacer clic en cualquier enlace válido
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                    loadingOverlay.style.display = 'flex';
                }
            });
        });

        // También mostrar spinner en enlaces de sugerencias (insertados dinámicamente)
        resultsContainer.addEventListener('click', (e) => {
            if (e.target.closest('a.sugerencia-link')) {
                loadingOverlay.style.display = 'flex';
            }
        });

        // Mostrar spinner en formularios normales
        document.querySelectorAll('form').forEach(form => {
            if (!form.hasAttribute('onsubmit') && !form.classList.contains('no-spinner')) {
                form.addEventListener('submit', () => {
                    loadingOverlay.style.display = 'flex';
                });
            }
        });
    });
</script>