<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UbigeoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteController;

Route::get('/', function () {
    return view('home');
});

Route::get('/', [ProductoController::class, 'mostrarProductosPublico']);


Route::prefix('acceso')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/signup', [UbigeoController::class, 'signup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signupPost'])->name('signup.post');
    Route::post('/login', [AuthController::class, 'loginPost']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// API para obtener distritos de una provincia
Route::get('/provincias/{provincia_id}/distritos', [UbigeoController::class, 'getDistritos']);

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin', function () {
        return redirect()->route('admin.productosAdmin');
    })->name('admin.home');

    // Eliminada ruta con función anónima para /admin/productos
    // Para evitar conflicto con la ruta que usa el controlador ProductoController@index

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboardAdmin'); // vista dashboard
    })->name('admin.dashboardAdmin');
    Route::get('/admin/productos', [ProductoController::class, 'index'])->name('admin.productosAdmin');
});

// Rutas que usan controlador para productos
Route::post('/productos/crear', [ProductoController::class, 'store'])->name('productos.store');
Route::put('/variantes/{id}/actualizar', [VarianteController::class, 'actualizarCantidad'])->name('variantes.actualizar');
Route::delete('/admin/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
Route::put('productos/{codigo}', [ProductoController::class, 'update'])->name('productos.update');
Route::get('/categoria/{nombre}', [ProductoController::class, 'filtrarPorCategoria']);
