@extends('layouts.app')
@section('content')
@include('components.navAdmin')

<div class="{{ request()->is('admin/dashboard') ? '' : 'p-3' }}">
    @yield('content')
</div>

<link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">
@endsection