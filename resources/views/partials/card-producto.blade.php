<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="text-center overflow-hidden" style="height: 250px;">
            <img
                src="{{ asset($producto->imagen) }}"
                alt="{{ $producto->nombre }}"
                class="card-img-top img-fluid"
                style="max-height: 100%; width: auto;">
        </div>
        <div class="card-body">
            <h6 class="card-title">{{ $producto->nombre }}</h6>
            <p class="text-muted small">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
            <span class="h5 text-primary">S/ {{ number_format($producto->precio, 2) }}</span>
        </div>
        <div class="card-footer bg-transparent">
            <a href="{{ route('producto.detalle', $producto->codigo) }}" class="btn btn-primary w-100">
                Ver Detalles
            </a>
        </div>
    </div>
</div>