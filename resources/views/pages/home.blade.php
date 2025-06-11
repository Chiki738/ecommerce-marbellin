@extends('layouts.app')

@section('content')
<div class="position-relative full-height w-100">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="
        background-image: url('/img/fondo.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        filter: brightness(40%);
        z-index: 1;">
    </div>

    <div class="position-relative z-2 d-flex justify-content-center align-items-center full-height">
        <div class="bg-dark bg-opacity-50 text-white rounded-4 text-center px-3 px-sm-5 py-4 w-100 mx-3" style="max-width: 750px;">
            <h1 class="titulo-responsivo">Bienvenido a Marbellin</h1>
            <p class="mt-3 parrafo-responsivo">
                Pasión, elegancia y confianza para la mujer moderna. <br>
                Desde hace 6 años, nos dedicamos a resaltar tu esencia con ropa interior que combina comodidad, estilo y sensualidad.
                Gracias por ser parte de esta historia llena de fuerza femenina. 💖 <br><br>
                <strong class="fs-6">¡Sigue brillando con nosotras!</strong>
            </p>
        </div>
    </div>
</div>
@endsection
