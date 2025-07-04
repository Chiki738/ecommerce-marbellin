@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2>Verificación en dos pasos</h2>
    <p>Revisa tu correo e ingresa el código:</p>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('2fa.verify.post') }}">
        @csrf
        <input type="text" name="code" maxlength="6" class="form-control text-center w-25 mx-auto" required>
        <button class="btn btn-success mt-3">Verificar</button>
    </form>
</div>
@endsection