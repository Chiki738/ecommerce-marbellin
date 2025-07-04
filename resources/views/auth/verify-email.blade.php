@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2>Verifica tu correo</h2>
    <p>Te enviamos un enlace de verificación. Revisa tu bandeja de entrada.</p>

    @if (session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="btn btn-primary mt-3">Reenviar correo</button>
    </form>

    <a href="{{ route('login') }}" class="btn btn-link mt-3">Volver al login</a>
</div>
@endsection