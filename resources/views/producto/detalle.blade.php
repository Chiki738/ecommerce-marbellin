    @extends('layouts.app')

    @section('content')
    <style>
        /* Estilos para tallas y colores (igual que el tuyo) */
        .tallas-container,
        .colores-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .color-circle {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid #ccc;
            margin-right: 8px;
            vertical-align: middle;
            box-sizing: border-box;
        }

        .color-blanco {
            background-color: #ffffff;
            border: 1px solid #999;
        }

        .color-negro {
            background-color: #000000;
        }

        .color-rojo {
            background-color: #ff0000;
        }

        .color-azul {
            background-color: #0000ff;
        }

        .color-verde {
            background-color: #008000;
        }

        .color-amarillo {
            background-color: #ffff00;
        }

        input[type="radio"] {
            display: none;
        }

        .talla-label,
        .colores-container label {
            display: flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background-color 0.2s, border-color 0.2s;
            user-select: none;
            cursor: pointer;
        }

        .color-name {
            margin-left: 4px;
            font-weight: 500;
            transition: color 0.2s, font-weight 0.2s;
        }

        input[type="radio"]:checked+label.talla-label {
            border: 2px solid #007bff;
            background-color: #e6f0ff;
            font-weight: 600;
        }

        input[type="radio"]:checked+label span.color-name {
            font-weight: 700;
            color: #007bff;
        }

        input[type="radio"]:checked+label .color-circle {
            border: 2px solid #007bff;
        }

        input[type="radio"]:disabled+label {
            color: gray;
            cursor: not-allowed;
        }

        input[type="radio"]:disabled+label .color-circle {
            opacity: 0.4;
            cursor: not-allowed;
        }
    </style>

    <div class="container mt-4 py-5">
        <div class="row">
            <div class="col-md-6 d-flex justify-content-center">
                <img
                    class="border border-dark border-2 rounded-3 shadow-lg mb-4"
                    src="{{ $producto->imagen }}"
                    alt="{{ $producto->nombre }}"
                    style="width:100%; aspect-ratio: 1 / 1; object-fit: cover;">
            </div>
            <div class="col-md-6">
                <h2 class="display-4 fw-bold">{{ $producto->nombre }}</h2>
                <p><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
                <p><strong>Precio:</strong> S/ {{ number_format($producto->precio, 2) }}</p>
                <p class="text">{{ $producto->descripcion }}</p>

                <form method="POST" action="{{ auth()->check() ? route('carrito.agregar') : url('/acceso/login') }}">
                    @csrf
                    <input type="hidden" name="producto_codigo" value="{{ $producto->codigo }}">

                    <div class="tallas-container">
                        @php
                        $tallas = $producto->variantes->pluck('talla')->unique();
                        @endphp
                        @foreach($tallas as $talla)
                        @php
                        $cantidadTalla = $producto->variantes->where('talla', $talla)->sum('cantidad');
                        @endphp
                        <input
                            type="radio"
                            name="talla"
                            id="talla_{{ $talla }}"
                            value="{{ $talla }}"
                            {{ $cantidadTalla == 0 ? 'disabled' : '' }}>
                        <label for="talla_{{ $talla }}" class="talla-label">
                            {{ $talla }}
                        </label>
                        @endforeach
                    </div>

                    <div class="colores-container mt-3">
                        @php
                        $colores = $producto->variantes->pluck('color')->unique();
                        @endphp
                        @foreach($colores as $color)
                        @php
                        $cantidadColor = $producto->variantes->where('color', $color)->sum('cantidad');
                        $colorClass = 'color-' . strtolower($color);
                        @endphp
                        <input
                            type="radio"
                            name="color"
                            id="color_{{ $color }}"
                            value="{{ $color }}"
                            {{ $cantidadColor == 0 ? 'disabled' : '' }}>
                        <label for="color_{{ $color }}">
                            <span class="color-circle {{ $colorClass }}"></span>
                            <span class="color-name">{{ ucfirst($color) }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" name="cantidad" id="cantidad" value="1" min="1" class="form-control" style="width: 100px;">
                    </div>

                    <button type="submit" class="btn btn-success mt-4" {{ auth()->check() ? '' : 'disabled' }}
                        {{ auth()->check() ? '' : 'title=Debes iniciar sesión para comprar' }}>
                        {{ auth()->check() ? 'Agregar al carrito' : 'Iniciar sesión para comprar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success') && auth()->check())
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11000;">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastEl = document.getElementById('successToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 3000, // 3 segundos
                    autohide: true
                });
                toast.show();
            }
        });
    </script>
    @endif

    @endsection