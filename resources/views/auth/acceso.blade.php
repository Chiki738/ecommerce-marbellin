@extends('layouts.app')

@section('content')
<div class="d-flex min-vh-100 justify-content-center align-items-center bg-light p-4">
    <div id="card" class="bg-white rounded shadow overflow-hidden" style="max-width: 700px; width: 100%;">
        <div class="row g-0">

            {{-- Columna lateral: opciones de acceso --}}
            <div class="col-md-4 bg-primary text-white d-flex flex-column justify-content-between p-4">
                <div>
                    <h4 class="fw-normal mb-3">¿Ya tienes cuenta?</h4>
                    <a href="{{ route('login') }}" class="btn btn-outline-light w-100 mb-4">Iniciar sesión</a>

                    <h4 class="fw-normal mb-3">¿Nuevo aquí?</h4>
                    <a href="{{ route('signup') }}" class="btn btn-outline-light w-100">Registrarse</a>
                </div>

                <a href="{{ url('/') }}" class="btn btn-light w-100 mt-4">← Regresar al inicio</a>
            </div>

            {{-- Contenido del formulario (login o registro) --}}
            <div class="col-md-8 p-4 position-relative">
                @foreach (['success', 'error'] as $type)
                @if (session($type))
                <div class="alert alert-{{ $type === 'success' ? 'success' : 'danger' }} text-center" role="alert">
                    {{ session($type) }}
                </div>
                @endif
                @endforeach

                @yield('formContent')
            </div>
        </div>
    </div>
</div>
@endsection