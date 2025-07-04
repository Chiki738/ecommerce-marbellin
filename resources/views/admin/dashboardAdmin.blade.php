@extends('layouts.app')

@section('content')
<style>
    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
    }

    iframe {
        width: 100%;
        height: 100vh;
        border: none;
        display: block;
    }
</style>

<iframe src="http://127.0.0.1:8050"></iframe>
@endsection