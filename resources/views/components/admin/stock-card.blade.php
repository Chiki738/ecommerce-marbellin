@props(['tipo', 'valor', 'color' => 'secondary', 'texto' => 'text-dark'])

<div class="col-md-6">
    <div class="card shadow-sm text-center bg-{{ $color }}">
        <div class="card-body">
            <h6 class="{{ $texto }}">Stock {{ $tipo }} ({{ $tipo == 'Crítico' ? '≤5' : '≤13' }})</h6>
            <h3 class="{{ $texto }} stock-{{ strtolower($tipo) }}-count">{{ $valor }}</h3>
        </div>
    </div>
</div>