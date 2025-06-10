@extends('admin.appAdmin')
@include('admin.modals.editarProducto')

@section('content')
<h2 class="text-center mb-4">Administración de los Productos</h2>

{{-- Alerta de éxito --}}
@if (session('success'))
<div class="position-absolute top-0 end-0 my-5 mx-3 z-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
</div>
@endif

{{-- Alerta global de stock (agrupado por producto y cantidad de variantes) --}}
@php
$stockAlert = []; // ['productoNombre' => ['bajo' => n, 'medio' => m]]
@endphp

@foreach ($productos as $producto)
@php
$bajoCount = 0;
$medioCount = 0;
@endphp
@foreach ($variantes as $variante)
@if ($variante->producto_codigo === $producto->codigo)
@if ($variante->cantidad <= 5)
    @php $bajoCount++; @endphp
    @elseif ($variante->cantidad <= 13)
        @php $medioCount++; @endphp
        @endif
        @endif
        @endforeach

        @if ($bajoCount> 0 || $medioCount > 0)
        @php
        $stockAlert[$producto->nombre] = ['bajo' => $bajoCount, 'medio' => $medioCount];
        @endphp
        @endif
        @endforeach

        @if (!empty($stockAlert))
        <div class="alert bg-warning bg-opacity-25 border-warning border-2 rounded" style="color: #5a4b00;">
            <strong style="color: #842029;">¡Advertencia de stock!</strong>
            <ul class="mb-0 mt-2" style="color: #5a4b00;">
                @foreach ($stockAlert as $nombreProducto => $cantidades)
                <li>
                    <strong style="color: #5a4b00;">{{ $nombreProducto }}:</strong>
                    @if ($cantidades['bajo'] > 0)
                    <span style="color: #a71d2a;">Stock muy bajo ({{ $cantidades['bajo'] }} variante{{ $cantidades['bajo'] > 1 ? 's' : '' }})</span>
                    @endif
                    @if ($cantidades['bajo'] > 0 && $cantidades['medio'] > 0)
                    &nbsp;|&nbsp;
                    @endif
                    @if ($cantidades['medio'] > 0)
                    <span style="color: #856404;">Stock moderado ({{ $cantidades['medio'] }} variante{{ $cantidades['medio'] > 1 ? 's' : '' }})</span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif


        {{-- Búsqueda y agregar producto --}}
        <div class="d-flex justify-content-around align-items-center mb-5">
            <input id="buscarProducto" class="form-control w-25 me-2" type="search" placeholder="Buscar por código o nombre" aria-label="Buscar" />
            <a href="#" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">Agregar Producto</a>
        </div>

        {{-- Acordeón productos --}}
        <div class="accordion accordion-flush" id="accordionFlushProductos">
            @foreach ($productos as $producto)
            @php
            $stockBajoCount = 0;
            $stockMedioCount = 0;
            $stockAltoCount = 0;

            foreach ($variantes as $variante) {
            if ($variante->producto_codigo === $producto->codigo) {
            if ($variante->cantidad <= 5) {
                $stockBajoCount++;
                } elseif ($variante->cantidad <= 13) {
                    $stockMedioCount++;
                    } else {
                    $stockAltoCount++;
                    }
                    }
                    }
                    @endphp

                    <div class="accordion-item mb-3 producto-item" data-codigo="{{ strtolower($producto->codigo) }}" data-nombre="{{ strtolower($producto->nombre) }}">
                    <div class="accordion-header border p-1 border-dark border-2 rounded bg-light" id="flush-heading{{ $loop->index }}">
                        <button class="accordion-button collapsed d-flex gap-4 bg-light" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#flush-collapse{{ $loop->index }}"
                            aria-expanded="false"
                            aria-controls="flush-collapse{{ $loop->index }}">
                            <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                            <div class="d-flex gap-5">
                                <div>
                                    <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                                    <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
                                    <p><strong>Precio:</strong> {{ number_format($producto->precio, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-danger"><strong>Stock Bajo:</strong> {{ $stockBajoCount }}</p>
                                    <p style="color: #cc9a06;"><strong>Stock Medio:</strong> {{ $stockMedioCount }}</p>
                                    <p class="text-success"><strong>Stock Alto:</strong> {{ $stockAltoCount }}</p>
                                </div>
                            </div>
                        </button>
                    </div>

                    <div id="flush-collapse{{ $loop->index }}" class="accordion-collapse collapse" aria-labelledby="flush-heading{{ $loop->index }}" data-bs-parent="#accordionFlushProductos">
                        <div class="accordion-body">
                            <div>
                                <p><strong>Categoría:</strong> {{ $producto->categoria }}</p>
                                <p><strong>Descripción:</strong><br>{{ $producto->descripcion }}</p>
                            </div>

                            <div class="d-flex flex-column gap-2 mt-3 text-center">
                                <a href="#" class="btn btn-warning btn-sm btnEditarProducto w-50 m-auto"
                                    data-codigo="{{ $producto->codigo }}"
                                    data-nombre="{{ $producto->nombre }}"
                                    data-precio="{{ $producto->precio }}"
                                    data-categoria="{{ $producto->categoria }}"
                                    data-descripcion="{{ $producto->descripcion }}"
                                    data-imagen="{{ Str::startsWith($producto->imagen, ['http://', 'https://']) ? $producto->imagen : asset('storage/' . $producto->imagen) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarProducto">
                                    Editar
                                </a>

                                <form action="{{ route('productos.destroy', $producto->codigo) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-50">Eliminar</button>
                                </form>
                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Talla</th>
                                            <th>Color</th>
                                            <th>Cantidad</th>
                                            <th>Creado</th>
                                            <th>Actualizado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($variantes as $variante)
                                        @if ($variante->producto_codigo == $producto->codigo)
                                        @php
                                        $class = '';
                                        if ($variante->cantidad <= 5) {
                                            $class='table-danger' ;
                                            } elseif ($variante->cantidad <= 13) {
                                                $class='table-warning' ;
                                                } else {
                                                $class='table-success' ;
                                                }
                                                @endphp
                                                <tr class="{{ $class }}">
                                                <td>{{ $variante->id }}</td>
                                                <td>{{ $variante->talla }}</td>
                                                <td>{{ $variante->color }}</td>
                                                <td>
                                                    <input type="number" value="{{ $variante->cantidad }}" class="form-control cantidad-input" data-id="{{ $variante->id }}" />
                                                </td>
                                                <td>{{ $variante->created_at }}</td>
                                                <td>{{ $variante->updated_at }}</td>
                                                <td class="d-flex gap-1">
                                                    <form method="POST" action="{{ route('variantes.actualizar', $variante->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-warning btn-sm actualizar-btn" disabled>Actualizar</button>
                                                    </form>
                                                </td>
                                                </tr>
                                                @endif
                                                @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
        </div>
        @endforeach
        </div>

        @include('admin.modals.agregarProducto')
        @endsection

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const inputBuscar = document.getElementById('buscarProducto');
                const productos = [...document.querySelectorAll('.producto-item')];

                const listaProductos = productos.map(producto => ({
                    element: producto,
                    codigo: producto.getAttribute('data-codigo'),
                    nombre: producto.getAttribute('data-nombre'),
                }));

                const fuse = new Fuse(listaProductos, {
                    keys: ['nombre'],
                    threshold: 0.4,
                    ignoreLocation: true,
                    getFn: (obj, path) => {
                        const value = Fuse.config.getFn(obj, path);
                        return value ? value.normalize("NFD").replace(/[̀-ͯ]/g, "") : value;
                    }
                });

                inputBuscar.addEventListener('input', () => {
                    let valor = inputBuscar.value.toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "").trim();

                    if (!valor) {
                        productos.forEach(p => p.style.display = '');
                        return;
                    }

                    const matchCodigo = listaProductos.find(p => p.codigo === valor);

                    if (matchCodigo) {
                        productos.forEach(p => {
                            p.style.display = p === matchCodigo.element ? '' : 'none';
                        });
                    } else {
                        const resultados = fuse.search(valor);
                        const encontrados = resultados.map(r => r.item.element);

                        productos.forEach(p => {
                            p.style.display = encontrados.includes(p) ? '' : 'none';
                        });
                    }
                });

                document.querySelectorAll('.btnEditarProducto').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const codigo = btn.getAttribute('data-codigo');
                        const nombre = btn.getAttribute('data-nombre');
                        const precio = btn.getAttribute('data-precio');
                        const categoria = btn.getAttribute('data-categoria');
                        const descripcion = btn.getAttribute('data-descripcion');
                        const imagenUrl = btn.getAttribute('data-imagen');

                        const form = document.getElementById('formEditarProducto');
                        form.action = `/productos/${codigo}`;

                        form.codigo.value = codigo;
                        form.nombre.value = nombre;
                        form.precio.value = precio;
                        form.categoria.value = categoria;
                        form.descripcion.value = descripcion;

                        const preview = document.querySelector('#previewImagen img');
                        if (imagenUrl) {
                            preview.src = imagenUrl;
                            preview.style.display = 'block';
                        } else {
                            preview.src = '';
                            preview.style.display = 'none';
                        }
                    });
                });

                document.querySelectorAll('.cantidad-input').forEach(input => {
                    const originalValue = input.value;
                    const row = input.closest('tr');
                    const updateBtn = row.querySelector('.actualizar-btn');

                    input.addEventListener('input', () => {
                        updateBtn.disabled = input.value === originalValue;

                        let hiddenInput = updateBtn.form.querySelector('input[name="cantidad"]');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'cantidad';
                            updateBtn.form.appendChild(hiddenInput);
                        }
                        hiddenInput.value = input.value;
                    });
                });
            });
        </script>
        @endpush