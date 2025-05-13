@extends('auth.acceso')

@section('formContent')
<form id="formSign" action="{{ route('signup') }}" method="POST" class="w-100 needs-validation p-sm-4 p-1" novalidate>
    @csrf
    <h3 class="text-center mb-3">Registrarse</h3>

    <div class="row g-3">
        <div class="col-sm-6 col-12 form-floating">
            <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Nombre" required>
            <label for="nombre">Primer nombre</label>
        </div>
        <div class="col-sm-6 col-12 form-floating">
            <input type="text" name="apellido" class="form-control" id="apellido" placeholder="Apellido" required>
            <label for="apellido">Primer apellido</label>
        </div>
    </div>

    <div class="form-floating mt-3">
        <input type="email" name="email" class="form-control" id="email" placeholder="Correo" required>
        <label for="email">Correo electrónico</label>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-12 col-sm-6 form-floating">
            <input type="password" name="password" class="form-control" id="pass" placeholder="Contraseña" required>
            <label for="pass">Contraseña</label>
        </div>
        <div class="col-12 col-sm-6 form-floating">
            <input type="password" name="password_confirmation" class="form-control" id="pass2" placeholder="Confirmar" required>
            <label for="pass2">Confirmar contraseña</label>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <label for="departamento" class="form-label">Departamento</label>
            <select id="departamento" name="departamento" class="form-select" required>
                <option value="">Seleccionar Departamento</option>
            </select>
        </div>

        <div class="col-12 mt-3">
            <label for="provincia" class="form-label">Provincia</label>
            <select id="provincia" name="provincia" class="form-select" required>
                <option value="">Seleccionar Provincia</option>
            </select>
        </div>

        <div class="col-12 mt-3">
            <label for="distrito" class="form-label">Distrito</label>
            <select id="distrito" name="distrito" class="form-select" required>
                <option value="">Seleccionar Distrito</option>
            </select>
        </div>
    </div>

    <div class="form-floating mt-3">
        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección exacta" required>
        <label for="direccion">Dirección (Google Maps)</label>
    </div>

    <button class="btn btn-success w-100 py-2 mt-4">Registrarse</button>
</form>

@endsection

@section('scripts')
<script src="{{ asset('js/ubigeo.js') }}"></script>
@endsection