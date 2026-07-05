@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <aside class="col-lg-3 mb-4">
            @include('partials.filtros')
        </aside>

        <section class="col-lg-9">
            <div class="catalog-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                <div>
                    <h1 class="h3 mb-1">Catálogo Marbellin</h1>
                    <p class="text-muted mb-0">Explora prendas disponibles por talla, color y categoría.</p>
                </div>
                <span class="badge bg-primary fs-6">{{ $productos->total() }} productos</span>
            </div>

            <div class="row g-4">
                @forelse($productos as $producto)
                @include('partials.card-producto', ['producto' => $producto])
                @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        No encontramos productos con esos filtros. Prueba con otra talla, color o categoría.
                    </div>
                </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $productos->appends(request()->query())->onEachSide(1)->links('vendor.pagination.bootstrap-5-es') }}
            </div>
        </section>
    </div>
</div>
@endsection
