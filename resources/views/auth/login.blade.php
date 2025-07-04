@extends('auth.acceso')

@section('formContent')
<form id="formLogin" action="{{ route('login') }}" method="POST" class="needs-validation p-3 p-sm-4" novalidate>
    @csrf
    <h3 class="text-center mb-4">Iniciar sesión</h3>

    @php
    $campos = [
    'email' => ['Correo electrónico', 'email', 'correo@example.com', 'Ingresa un correo válido.'],
    'password' => ['Contraseña', 'password', 'Contraseña', 'La contraseña es obligatoria.']
    ];
    @endphp

    @foreach ($campos as $name => [$label, $type, $placeholder, $invalid])
    <div class="form-floating mb-{{ $loop->last ? 4 : 3 }}">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="login{{ ucfirst($name) }}"
            class="form-control"
            placeholder="{{ $placeholder }}"
            required>
        <label for="login{{ ucfirst($name) }}">{{ $label }}</label>
        <div class="invalid-feedback">{{ $invalid }}</div>
    </div>
    @endforeach

    <button type="submit" class="btn btn-success w-100 py-2">Entrar</button>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/login.js') }}"></script>
@endpush