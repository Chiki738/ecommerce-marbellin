@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<section class="home-banner d-flex justify-content-center align-items-center">
    <div class="content-box text-white text-center px-3 px-sm-5 py-4 w-100 mx-3">
        <h1 class="titulo-responsivo">Bienvenido a Marbellin</h1>
        <p class="mt-3 parrafo-responsivo">
            Pasión, elegancia y confianza para la mujer moderna. <br>
            Desde hace 6 años, nos dedicamos a resaltar tu esencia con ropa interior que combina comodidad, estilo y sensualidad.
            Gracias por ser parte de esta historia llena de fuerza femenina. 💖 <br><br>
            <strong class="fs-6">¡Sigue brillando con nosotras!</strong>
        </p>
    </div>
</section>
@endsection