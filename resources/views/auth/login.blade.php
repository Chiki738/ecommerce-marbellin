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
                {{-- Aquí se cargará el formulario de login --}}
                <form id="formLogin" action="{{ route('login') }}" method="POST" class="w-100 needs-validation p-sm-4 p-1" novalidate>
                    @csrf
                    <h3 class="text-center mb-4">Iniciar sesión</h3>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="loginEmail" placeholder="correo@example.com" required>
                        <label for="loginEmail">Correo electrónico</label>
                        <div class="invalid-feedback">Ingresa un correo válido.</div>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="loginPass" placeholder="Contraseña" required>
                        <label for="loginPass">Contraseña</label>
                        <div class="invalid-feedback">La contraseña es obligatoria.</div>
                    </div>

                    <button class="btn btn-primary w-100 py-2">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection