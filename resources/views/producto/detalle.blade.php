@extends('layouts.app')

@section('content')
<style>
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
            <img class="border border-dark border-2 rounded-3 shadow-lg mb-4"
                src="{{ $producto->imagen }}"
                alt="{{ $producto->nombre }}"
                style="width:100%; aspect-ratio: 1 / 1; object-fit: cover;">
        </div>
        <div class="col-md-6">
            <h2 class="h1 fw-bold mb-3">{{ $producto->nombre }}</h2>
            <p class="text-muted"><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
            <p class="text">{{ $producto->descripcion }}</p>
            <div class="mb-3">
                <span class="h4 text-primary">S/ {{ number_format($producto->precio, 2) }}</span>
            </div>

            <form id="formAgregarCarrito" method="POST" action="{{ route('carrito.agregar') }}">
                @csrf
                <input type="hidden" name="producto_codigo" value="{{ $producto->codigo }}">

                <div class="mb-3">
                    <h6 class="fw-bold">Talla:</h6>
                    <div class="btn-group" role="group">
                        @foreach($producto->variantes->pluck('talla')->unique() as $talla)
                        @php $stock = $producto->variantes->where('talla', $talla)->sum('cantidad'); @endphp
                        <input type="radio" class="btn-check" name="talla" id="talla_{{ $talla }}" value="{{ $talla }}" {{ $stock == 0 ? 'disabled' : '' }}>
                        <label class="btn btn-outline-primary" for="talla_{{ $talla }}">{{ $talla }}</label>
                        @endforeach
                    </div>
                </div>

                <div class="colores-container mt-3">
                    @php $colores = $producto->variantes->pluck('color')->unique(); @endphp
                    @foreach($colores as $color)
                    @php
                    $cantidadColor = $producto->variantes->where('color', $color)->sum('cantidad');
                    $colorClass = 'color-' . strtolower($color);
                    @endphp
                    <input type="radio" name="color" id="color_{{ $color }}" value="{{ $color }}" {{ $cantidadColor == 0 ? 'disabled' : '' }}>
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

                <button type="submit" class="btn btn-success mt-4" {{ auth()->check() ? '' : 'disabled title=Debes iniciar sesión para comprar' }}>
                    {{ auth()->check() ? 'Agregar al carrito' : 'Iniciar sesión para comprar' }}
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast de éxito -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11000;">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="successToastMsg">
                Producto agregado al carrito
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formAgregarCarrito');
        const toastEl = document.getElementById('successToast');
        const toastMsg = document.getElementById('successToastMsg');
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const token = document.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest', // <- ESTA LÍNEA ES CLAVE
                        'Accept': 'application/json'
                    },
                    body: formData
                });


                const data = await response.json();

                if (response.ok) {
                    toastMsg.innerText = data.message || 'Producto agregado al carrito';
                    toast.show();
                } else {
                    alert(data.error || 'Error al agregar al carrito');
                }

            } catch (error) {
                console.error(error);
                alert('Ocurrió un error inesperado');
            }
        });
    });
</script>
@endsection