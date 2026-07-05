<div class="col-md-6 col-lg-4">
    <article class="card product-card h-100 shadow-sm overflow-hidden">
        <div class="product-card-image">
            <img
                src="{{ asset($producto->imagen) }}"
                alt="{{ $producto->nombre }}"
                class="card-img-top">
        </div>
        <div class="card-body">
            <p class="text-muted small text-uppercase fw-semibold mb-2">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
            <h2 class="h6 card-title mb-3">{{ $producto->nombre }}</h2>
            <span class="h5 text-primary">S/ {{ number_format($producto->precio, 2) }}</span>
        </div>
        <div class="card-footer bg-transparent">
            <a href="{{ route('producto.detalle', $producto->codigo) }}" class="btn btn-primary w-100">
                Ver detalle
            </a>
        </div>
    </article>
</div>
