@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<section class="home-banner d-flex justify-content-center align-items-center text-white text-center">
    <div class="content-box px-3 px-sm-5 py-4 w-100">
        <h1 class="titulo-responsivo">Bienvenida a Marbellin</h1>
        <p class="mt-3 parrafo-responsivo">
            Pasión, elegancia y confianza para la mujer moderna. <br>
            Desde hace 6 años, resaltamos tu esencia con ropa interior que combina comodidad, estilo y sensualidad. <br><br>
            <strong class="fs-6">¡Gracias por ser parte de esta historia llena de fuerza femenina! 💖</strong> <br>
            <strong class="fs-6">¡Sigue brillando con nosotras!</strong>
        </p>
    </div>
</section>
@endsection