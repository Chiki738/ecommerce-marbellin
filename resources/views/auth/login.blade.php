@extends('auth.acceso')

@section('formContent')
<form id="formLogin" action="{{ route('login') }}" method="POST" class="needs-validation p-3 p-sm-4" novalidate>
    @csrf
    <h3 class="text-center mb-4">Iniciar sesión</h3>

    @php
    $campos = [
    'email' => ['label' => 'Correo electrónico', 'type' => 'email', 'placeholder' => 'correo@example.com', 'feedback' => 'Ingresa un correo válido.'],
    'password' => ['label' => 'Contraseña', 'type' => 'password', 'placeholder' => 'Contraseña', 'feedback' => 'La contraseña es obligatoria.']
    ];
    @endphp

    @foreach ($campos as $name => $campo)
    <div class="form-floating mb-{{ $loop->last ? 4 : 3 }}">
        <input
            type="{{ $campo['type'] }}"
            name="{{ $name }}"
            id="login{{ ucfirst($name) }}"
            class="form-control"
            placeholder="{{ $campo['placeholder'] }}"
            required>
        <label for="login{{ ucfirst($name) }}">{{ $campo['label'] }}</label>
        <div class="invalid-feedback">{{ $campo['feedback'] }}</div>
    </div>
    @endforeach

    <button type="submit" class="btn btn-success w-100 py-2">Entrar</button>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/login.js') }}"></script>
@endpush