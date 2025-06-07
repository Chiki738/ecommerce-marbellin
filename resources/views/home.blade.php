@extends('layouts.app')

@section('content')
<div class="position-relative" style="height: 100vh; width: 100%;">
    <!-- Fondo con imagen -->
    <div style="
        background-image: url('/img/fondo.jpg');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        height: 100%;
        width: 100%;
        position: absolute;
        filter: brightness(40%);
        top: 0;
        left: 0;
        z-index: 1;">
    </div>

    <!-- Contenido con fondo semitransparente -->
    <div class="d-flex align-items-center justify-content-center position-relative" style="height: 100%; z-index: 2;">
        <div class="bg-dark bg-opacity-50 text-white p-5 rounded-4 w-75 text-center">
            <h1 class="display-2 fw-bold">Bienvenido a Marbellin</h1>
            <p class="mt-3 fs-5">
                Pasión, elegancia y confianza para la mujer moderna. <br>
                Desde hace 6 años, nos dedicamos a resaltar tu esencia con ropa interior que combina comodidad, estilo y sensualidad.
                Gracias por ser parte de esta historia llena de fuerza femenina. 💖 <br><br>
                <strong class="fs-6">¡Sigue brillando con nosotras!</strong>
            </p>
        </div>
    </div>
</div>
@endsection