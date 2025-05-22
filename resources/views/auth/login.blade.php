{{-- resources/views/auth/login.blade.php --}}
@extends('auth.acceso')

@section('formContent')
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

    <button class="btn btn-success w-100 py-2">Entrar</button>
</form>
@endsection