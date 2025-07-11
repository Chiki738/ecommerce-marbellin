@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2>Verifica tu correo</h2>
    <p class="mb-4">Te enviamos un enlace de verificación. Revisa tu bandeja de entrada.</p>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
        @csrf
        <button class="btn btn-primary">Reenviar correo</button>
    </form>

    <a href="{{ route('login') }}" class="btn btn-link">Volver al login</a>
</div>
@endsection