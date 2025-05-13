@extends('layouts.app')

@section('content')
<div class="d-flex min-vh-100 justify-content-center align-items-center bg-light p-5">
    <div id="card" class="bg-white rounded shadow overflow-hidden" style="max-width: 700px; width:100%">
        {{-- Barra lateral con botones --}}
        <div class="row g-0">
            <div class="col-md-4 bg-primary text-white d-flex flex-column justify-content-between align-items-center p-4">
                <div>
                    <h4 class="fw-normal mb-3">¿Ya tienes cuenta?</h4>
                    <a href="{{ route('login') }}" class="btn btn-outline-light w-100 mb-4">Iniciar sesión</a>

                    <h4 class="fw-normal mb-3">¿Nuevo aquí?</h4>
                    <a href="{{ route('signup') }}" class="btn btn-outline-light w-100">Registrarse</a>
                </div>
                <a href="{{ url('/') }}" class="btn btn-light mt-4 w-80">← Regresar al inicio</a>
            </div>

            <div class="col-md-8 p-4 position-relative">
                {{-- Aquí se cargará el formulario según la vista --}}
                @yield('formContent')
            </div>
        </div>
    </div>
</div>
@endsection