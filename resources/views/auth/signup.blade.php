@extends('auth.acceso')

@section('formContent')

@if ($errors->any())
<div class="alert alert-danger mt-3">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form id="formSign" action="{{ route('signup.post') }}" method="POST" class="needs-validation p-3 p-sm-4" novalidate>
    @csrf
    <h3 class="text-center mb-3">Registrarse</h3>

    @php
    $campos = [
    ['name' => 'nombre', 'label' => 'Primer nombre', 'type' => 'text', 'col' => 6],
    ['name' => 'apellido', 'label' => 'Primer apellido', 'type' => 'text', 'col' => 6],
    ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'col' => 12],
    ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password', 'col' => 6],
    ['name' => 'password_confirmation', 'label' => 'Confirmar contraseña', 'type' => 'password', 'col' => 6],
    ['name' => 'direccion', 'label' => 'Dirección exacta', 'type' => 'text', 'col' => 12],
    ];
    @endphp

    <div class="row g-3">
        @foreach ($campos as $campo)
        <div class="col-sm-{{ $campo['col'] }} form-floating">
            <input type="{{ $campo['type'] }}"
                name="{{ $campo['name'] }}"
                id="{{ $campo['name'] }}"
                class="form-control"
                placeholder="{{ $campo['label'] }}"
                required
                autocomplete="off">
            <label for="{{ $campo['name'] }}">{{ $campo['label'] }}</label>
        </div>
        @endforeach
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <label for="provincia" class="form-label">Provincia</label>
            <select id="provincia" name="provincia" class="form-select" required>
                <option value="">Seleccionar Provincia</option>
                @foreach($provincias as $provincia)
                <option value="{{ $provincia->provincia_id }}">{{ $provincia->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 mt-3">
            <label for="distrito" class="form-label">Distrito</label>
            <select id="distrito" name="distrito" class="form-select" required>
                <option value="">Seleccionar Distrito</option>
            </select>
        </div>
    </div>

    <button class="btn btn-success w-100 py-2 mt-4">Registrarse</button>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/signup.js') }}"></script>
@endpush