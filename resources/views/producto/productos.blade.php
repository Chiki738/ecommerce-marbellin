@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Filtros laterales -->
        <aside class="col-lg-3 mb-4">
            @include('partials.filtros')
        </aside>

        <!-- Catálogo de productos -->
        <section class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Productos ({{ $productos->total() }})</h4>
            </div>

            <div class="row g-4">
                @foreach($productos as $producto)
                @include('partials.card-producto', ['producto' => $producto])
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $productos->appends(request()->query())->onEachSide(1)->links('vendor.pagination.bootstrap-5-es') }}
            </div>
        </section>
    </div>
</div>
@endsection