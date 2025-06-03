@extends('admin.appAdmin')

@section('content')
<h2>Bienvenido al Módulo Analítico</h2>
<h3>Panel Analítico</h3>

<iframe
    src="http://localhost:8050/dashboard/"
    width="100%"
    height="800"
    style="border:none;">
</iframe>
@endsection