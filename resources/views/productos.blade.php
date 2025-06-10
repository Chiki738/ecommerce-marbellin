@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5" style="min-height: 100vh;">
    <div class="row">
        @foreach($productos as $producto)
        <div class="col-md-4 mb-4">
            <div class="card shadow" style="width: 18rem;">
                <img src="{{ asset($producto->imagen) }}"
                    class="card-img-top"
                    alt="{{ $producto->nombre }}"
                    style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover;">
                <div class="card-body">
                    <h6 class="card-title fw-bold">{{ $producto->nombre }}</h6>
                    <p class="card-text">
                        <strong>Categoría:</strong> {{ ucwords(str_replace('_', ' ', $producto->categoria)) }}<br>
                        <strong>Precio:</strong> S/ {{ number_format($producto->precio, 2) }}
                    </p>
                    <a href="{{ route('producto.detalle', $producto->codigo) }}" class="btn btn-warning">Ver producto</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Spinner de carga global -->
<div id="loading-overlay" style="display: none;">
    <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<!-- Estilos del spinner -->
<style>
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
        z-index: 9999;
    }
</style>

<!-- Script para mostrar el spinner al navegar -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('loading-overlay');

        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (
                    href &&
                    !href.startsWith('#') &&
                    !href.startsWith('javascript:') &&
                    !link.classList.contains('no-spinner')
                ) {
                    overlay.style.display = 'flex';
                }
            });
        });

        document.querySelectorAll('form').forEach(form => {
            if (!form.hasAttribute('onsubmit') && !form.classList.contains('no-spinner')) {
                form.addEventListener('submit', () => {
                    overlay.style.display = 'flex';
                });
            }
        });
    });
</script>
@endsection