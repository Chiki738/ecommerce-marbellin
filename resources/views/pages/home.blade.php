@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<section class="home-banner text-white">
    <div class="container home-hero-content">
        <div class="col-lg-7">
            <span class="home-eyebrow">Lencería fina en Lima</span>
            <h1 class="titulo-responsivo">Marbellin</h1>
            <p class="mt-3 parrafo-responsivo">
                Prendas delicadas, cómodas y elegantes para sentirte segura todos los días.
                Elige tu talla, revisa disponibilidad y compra en línea con una experiencia simple.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                <a href="{{ route('productos.vista') }}" class="btn btn-primary btn-lg">Ver catálogo</a>
                <a href="{{ url('/acceso/signup') }}" class="btn btn-outline-light btn-lg">Crear cuenta</a>
            </div>
        </div>
    </div>
</section>
@endsection
