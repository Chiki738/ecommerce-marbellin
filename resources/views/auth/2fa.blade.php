@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2>Verificación en dos pasos</h2>
    <p>Revisa tu correo e ingresa el código:</p>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('2fa.verify.post') }}" class="mt-4">
        @csrf
        <div class="mb-3">
            <input
                type="text"
                name="code"
                maxlength="6"
                class="form-control text-center mx-auto"
                style="max-width: 150px;"
                placeholder="123456"
                required>
        </div>

        <button class="btn btn-success">Verificar</button>
    </form>
</div>
@endsection