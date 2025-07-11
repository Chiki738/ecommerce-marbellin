@extends('auth.acceso')

@section('formContent')
<form id="formSign" class="needs-validation p-3 p-sm-4" novalidate>
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
        @foreach ($campos as $c)
        <div class="col-sm-{{ $c['col'] }} form-floating">
            <input type="{{ $c['type'] }}" name="{{ $c['name'] }}" id="{{ $c['name'] }}"
                class="form-control" placeholder="{{ $c['label'] }}" required autocomplete="off">
            <label for="{{ $c['name'] }}">{{ $c['label'] }}</label>
        </div>
        @endforeach
    </div>

    <div class="row mt-3">
        @php
        $selects = [
        'provincia' => ['label' => 'Provincia', 'options' => $provincias, 'id' => 'provincia', 'optionKey' => 'provincia_id'],
        'distrito' => ['label' => 'Distrito', 'options' => [], 'id' => 'distrito']
        ];
        @endphp

        @foreach ($selects as $name => $config)
        <div class="col-12 mt-{{ $loop->first ? 0 : 3 }}">
            <label for="{{ $config['id'] }}" class="form-label">{{ $config['label'] }}</label>
            <select id="{{ $config['id'] }}" name="{{ $name }}" class="form-select" required>
                <option value="">Seleccionar {{ $config['label'] }}</option>
                @foreach ($config['options'] ?? [] as $opt)
                <option value="{{ $opt[$config['optionKey']] ?? '' }}">{{ $opt->nombre ?? '' }}</option>
                @endforeach
            </select>
        </div>
        @endforeach
    </div>

    <button class="btn btn-success w-100 py-2 mt-4">Registrarse</button>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/signup.js') }}"></script>
@endpush