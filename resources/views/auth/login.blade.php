@extends('auth.acceso')

@section('formContent')
<form id="formLogin" action="{{ route('login') }}" method="POST" class="needs-validation p-3 p-sm-4" novalidate>
    @csrf
    <h3 class="text-center mb-4">Iniciar sesión</h3>

    @php
    $campos = [
    ['name' => 'email', 'type' => 'email', 'label' => 'Correo electrónico', 'placeholder' => 'correo@example.com', 'invalid' => 'Ingresa un correo válido.'],
    ['name' => 'password', 'type' => 'password', 'label' => 'Contraseña', 'placeholder' => 'Contraseña', 'invalid' => 'La contraseña es obligatoria.']
    ];
    @endphp

    @foreach ($campos as $campo)
    <div class="form-floating mb-{{ $loop->last ? 4 : 3 }}">
        <input type="{{ $campo['type'] }}" name="{{ $campo['name'] }}" id="login{{ ucfirst($campo['name']) }}"
            class="form-control" placeholder="{{ $campo['placeholder'] }}" required>
        <label for="login{{ ucfirst($campo['name']) }}">{{ $campo['label'] }}</label>
        <div class="invalid-feedback">{{ $campo['invalid'] }}</div>
    </div>
    @endforeach

    <button class="btn btn-success w-100 py-2">Entrar</button>
</form>
@endsection