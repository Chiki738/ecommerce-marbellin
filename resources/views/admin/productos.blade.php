@extends('admin.appAdmin')
@include('admin.modals.editarProducto')

@section('content')
<h2 class="text-center mb-3">Página de Productos</h2>

{{-- Alerta de éxito --}}
@if (session('success'))
<div class="position-absolute top-0 end-0 my-5 mx-3 z-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
</div>
@endif

{{-- Alerta de stock bajo --}}
@php $stockBajo = []; @endphp

@foreach ($productos as $producto)
@foreach ($variantes as $variante)
@if ($variante->producto_codigo === $producto->codigo && $variante->cantidad < 10)
    @php $stockBajo[]="Producto: {$producto->nombre}, Color: {$variante->color}, Talla: {$variante->talla}" ; @endphp
    @endif
    @endforeach
    @endforeach

    @if (!empty($stockBajo))
    <div class="alert alert-danger">
    <strong>¡Advertencia!</strong> Estas variantes tienen stock bajo:
    <ul class="mb-0">
        @foreach ($stockBajo as $mensaje)
        <li>{{ $mensaje }}</li>
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
        <div class="accordion-item mb-3 producto-item" data-codigo="{{ strtolower($producto->codigo) }}" data-nombre="{{ strtolower($producto->nombre) }}">
            <div class="accordion-header border p-1 border-dark border-2 rounded" id="flush-heading{{ $loop->index }}">
                <button class="accordion-button collapsed d-flex gap-4" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#flush-collapse{{ $loop->index }}"
                    aria-expanded="false"
                    aria-controls="flush-collapse{{ $loop->index }}">
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="img-thumbnail" style="max-width: 200px;">
                    <div>
                        <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                        <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
                        <p><strong>Precio:</strong> {{ number_format($producto->precio, 2) }}</p>
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
                            data-imagen="{{ asset('storage/' . $producto->imagen) }}"
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
                                <tr @if ($variante->cantidad < 10) class="table-danger" @endif>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputBuscar = document.getElementById('buscarProducto');
            const productos = document.querySelectorAll('.producto-item');

            inputBuscar.addEventListener('input', () => {
                const valor = inputBuscar.value.toLowerCase();
                productos.forEach(producto => {
                    const codigo = producto.getAttribute('data-codigo');
                    const nombre = producto.getAttribute('data-nombre');
                    producto.style.display = (codigo.includes(valor) || nombre.includes(valor)) ? '' : 'none';
                });
            });

            // Agregar evento click a todos los botones "Editar"
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

                    // Llenar campos
                    form.codigo.value = codigo;
                    form.nombre.value = nombre;
                    form.precio.value = precio;
                    form.categoria.value = categoria;
                    form.descripcion.value = descripcion;

                    // Vista previa imagen
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

            // Habilitar botón actualizar si cambia cantidad
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