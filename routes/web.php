<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UbigeoController;

Route::get('/', function () {
    return view('home');
});

// Rutas para el acceso
Route::prefix('acceso')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));  // Redirige automáticamente al login
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::get('/signup', fn() => view('auth.signup'))->name('signup');
});

Route::get('/ubigeo/departamentos', [UbigeoController::class, 'departamentos']);
Route::get('/ubigeo/provincias/{idDepartamento}', [UbigeoController::class, 'provincias']);
Route::get('/ubigeo/distritos/{idProvincia}', [UbigeoController::class, 'distritos']);
