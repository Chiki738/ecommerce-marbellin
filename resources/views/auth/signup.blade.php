@extends('auth.acceso')

@section('formContent')
<form id="formSign" action="{{ route('signup.post') }}" method="POST" class="w-100 needs-validation p-sm-4 p-1" novalidate>
    @csrf
    <h3 class="text-center mb-3">Registrarse</h3>

    {{-- Mensajes de éxito o error --}}
    @if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Datos personales -->
    <div class="row g-3">
        <div class="col-sm-6 form-floating">
            <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Nombre" required>
            <label for="nombre">Primer nombre</label>
        </div>
        <div class="col-sm-6 form-floating">
            <input type="text" name="apellido" class="form-control" id="apellido" placeholder="Apellido" required>
            <label for="apellido">Primer apellido</label>
        </div>
    </div>

    <!-- Email -->
    <div class="form-floating mt-3">
        <input type="email" name="email" class="form-control" id="email" placeholder="Correo" required>
        <label for="email">Correo electrónico</label>
    </div>

    <!-- Contraseña -->
    <div class="row g-3 mt-3">
        <div class="col-sm-6 form-floating">
            <input type="password" name="password" class="form-control" id="pass" placeholder="Contraseña" required>
            <label for="pass">Contraseña</label>
        </div>
        <div class="col-sm-6 form-floating">
            <input type="password" name="password_confirmation" class="form-control" id="pass2" placeholder="Confirmar" required>
            <label for="pass2">Confirmar contraseña</label>
        </div>
    </div>

    <!-- Provincia y distrito -->
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

    <!-- Dirección -->
    <div class="form-floating mt-3">
        <input type="text" name="direccion" class="form-control" id="direccion" placeholder="Dirección" autocomplete="off" required>
        <label for="direccion">Dirección exacta</label>
    </div>

    <button class="btn btn-success w-100 py-2 mt-4">Registrarse</button>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const provinciaSelect = document.getElementById("provincia");
        const distritoSelect = document.getElementById("distrito");

        provinciaSelect.addEventListener("change", function() {
            const provinciaId = this.value;
            distritoSelect.innerHTML = provinciaId ?
                '<option value="">Cargando...</option>' :
                '<option value="">Seleccionar Distrito</option>';

            if (!provinciaId) return;

            fetch(`/provincias/${provinciaId}/distritos`)
                .then(res => res.json())
                .then(data => {
                    distritoSelect.innerHTML = '<option value="">Seleccionar Distrito</option>';
                    data.forEach(d => {
                        distritoSelect.innerHTML += `<option value="${d.distrito_id}">${d.nombre}</option>`;
                    });
                })
                .catch(() => {
                    distritoSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });
    });
</script>

<script>
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', e => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    })();
</script>
@endpush