@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<iframe
    src="{{ config('services.dashboard.url') }}"
    title="Dashboard administrativo Marbellin"
    class="admin-dashboard-frame"
    referrerpolicy="no-referrer">
</iframe>
@endsection
