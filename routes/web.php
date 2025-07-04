<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UbigeoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PagoController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::view('/', 'pages.home')->name('pages.home');

Route::prefix('productos')->group(function () {
    Route::get('/', [ProductoController::class, 'mostrarProductosPublico'])->name('productos.vista');
    Route::get('/autocomplete', [ProductoController::class, 'autocomplete'])->name('productos.autocomplete');
    Route::get('/filtrar', [ProductoController::class, 'filtrar'])->name('productos.filtrar');
    Route::get('/buscar', [ProductoController::class, 'autocomplete']);
    Route::get('/{codigo}', [ProductoController::class, 'detalleProducto'])->name('producto.detalle');
});

Route::get('/provincias/{provincia_id}/distritos', [UbigeoController::class, 'getDistritos']);

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/
Route::prefix('acceso')->group(function () {
    Route::redirect('/', '/acceso/login');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost']);
    Route::get('/signup', [UbigeoController::class, 'signup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signupPost'])->name('signup.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Verificación de correo
    Route::get('/email/verify', fn() => view('auth.verify-email'))->middleware('auth')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'El enlace de verificación fue reenviado.');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    // 2FA unificado
    Route::view('/2fa', 'auth.2fa')->name('2fa.verify');
    Route::post('/2fa', [AuthController::class, 'verify2FA'])->name('2fa.verify.post');
});

/*
|--------------------------------------------------------------------------
| Rutas de Usuarios Autenticados y Verificados
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('carrito')->group(function () {
        Route::get('/', [PedidoController::class, 'carrito'])->name('carrito');
        Route::post('/agregar', [PedidoController::class, 'agregarAlCarrito'])->name('carrito.agregar');
        Route::put('/actualizar/{id}', [PedidoController::class, 'actualizarCantidad'])->name('carrito.actualizar');
        Route::delete('/eliminar/{id}', [PedidoController::class, 'eliminar'])->name('carrito.eliminar');
        Route::delete('/vaciar', [PedidoController::class, 'vaciar'])->name('carrito.vaciar');
        Route::post('/checkout', [PedidoController::class, 'checkout'])->name('carrito.checkout');
    });

    Route::get('/pago/exito', [PagoController::class, 'exito'])->name('pago.exito');
});

/*
|--------------------------------------------------------------------------
| Rutas de Administrador Autenticado y Verificado
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:admin', 'verified'])->group(function () {
    Route::redirect('/', '/admin/productos')->name('admin.home');

    Route::view('/dashboard', 'admin.dashboardAdmin')->name('admin.dashboardAdmin');

    // Productos
    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('admin.productosAdmin');
        Route::post('/crear', [ProductoController::class, 'store'])->name('productos.store');
        Route::put('/{codigo}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/{codigo}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });

    // Variantes
    Route::put('/variantes/{id}/actualizar', [VarianteController::class, 'actualizarCantidad'])->name('variantes.actualizar');

    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('admin.pedidosAdmin');
});
