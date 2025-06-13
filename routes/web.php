<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UbigeoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteController;
use App\Http\Controllers\PedidoController;

// Ruta de inicio (solo presentación visual)
Route::get('/', function () {
    return view('pages.home');
})->name('pages.home');

// Ruta para ver productos públicos
Route::get('/productos', [ProductoController::class, 'mostrarProductosPublico'])->name('productos.vista');

Route::prefix('acceso')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/signup', [UbigeoController::class, 'signup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signupPost'])->name('signup.post');
    Route::post('/login', [AuthController::class, 'loginPost']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Obtener distritos desde una provincia
Route::get('/provincias/{provincia_id}/distritos', [UbigeoController::class, 'getDistritos']);

// Rutas protegidas para administrador
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin', fn() => redirect()->route('admin.productosAdmin'))->name('admin.home');
    Route::get('/admin/dashboard', fn() => view('admin.dashboardAdmin'))->name('admin.dashboardAdmin');
    Route::get('/admin/productosAdmin', [ProductoController::class, 'index'])->name('admin.productosAdmin');
});

// CRUD productos y variantes
Route::post('/productos/crear', [ProductoController::class, 'store'])->name('productos.store');
Route::put('/variantes/{id}/actualizar', [VarianteController::class, 'actualizarCantidad'])->name('variantes.actualizar');
Route::delete('/admin/productos/{codigo}', [ProductoController::class, 'destroy'])->name('productos.destroy');
Route::put('/productos/{codigo}', [ProductoController::class, 'update'])->name('productos.update');

Route::get('/filtrar', [ProductoController::class, 'filtrar'])->name('productos.filtrar');
Route::get('/producto/{codigo}', [ProductoController::class, 'detalleProducto'])->name('producto.detalle');

// Rutas protegidas para usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::get('/carrito', [PedidoController::class, 'index'])->name('carrito');
    Route::post('/carrito/agregar', [PedidoController::class, 'agregarAlCarrito'])->name('carrito.agregar');
    Route::put('/carrito/actualizar/{id}', [PedidoController::class, 'actualizarCantidad'])->name('carrito.actualizar');
    Route::delete('/carrito/eliminar/{id}', [PedidoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/checkout', [PedidoController::class, 'checkout'])->name('carrito.checkout');
});

// Autocomplete para productos
Route::get('/productos/autocomplete', [ProductoController::class, 'autocomplete'])->name('productos.autocomplete');
Route::get('/buscar', [ProductoController::class, 'autocomplete']);
