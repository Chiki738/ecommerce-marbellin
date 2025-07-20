<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\{
    AuthController,
    UbigeoController,
    ProductoController,
    VarianteController,
    PedidoController,
    PagoController,
    CambioProductoController
};

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::view('/', 'pages.home')->name('pages.home');

Route::prefix('productos')->controller(ProductoController::class)->group(function () {
    Route::get('/', 'mostrarProductosPublico')->name('productos.vista');
    Route::get('/filtrar', 'filtrar')->name('productos.filtrar');
    Route::get('/autocomplete', 'autocomplete')->name('productos.autocomplete');
    Route::get('/{codigo}', 'detalleProducto')->name('producto.detalle');
});

Route::get('/provincias/{provincia_id}/distritos', [UbigeoController::class, 'getDistritos']);

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/
Route::prefix('acceso')->group(function () {
    Route::redirect('/', '/acceso/login');

    // Login & Registro
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost']);
    Route::get('/signup', [UbigeoController::class, 'signup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signupPost'])->name('signup.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Verificación de correo
    Route::middleware('auth')->group(function () {
        Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('success', 'El enlace de verificación fue reenviado.');
        })->middleware('throttle:6,1')->name('verification.send');
    });

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        return $request->fulfill() ?: redirect('/');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    // 2FA
    Route::view('/2fa', 'auth.2fa')->name('2fa.verify');
    Route::post('/2fa', [AuthController::class, 'verify2FA'])->name('2fa.verify.post');
});

/*
|--------------------------------------------------------------------------
| Rutas para Usuarios Autenticados y Verificados
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('carrito')->controller(PedidoController::class)->group(function () {
        Route::get('/', 'carrito')->name('carrito');
        Route::post('/agregar', 'agregarAlCarrito')->name('carrito.agregar');
        Route::put('/actualizar/{id}', 'actualizarCantidad')->name('carrito.actualizar');
        Route::delete('/eliminar/{id}', 'eliminar')->name('carrito.eliminar');
        Route::delete('/vaciar', 'vaciar')->name('carrito.vaciar');
        Route::post('/checkout', 'checkout')->name('carrito.checkout');
    });

    Route::get('/verificar-stock/{pedido}', [PagoController::class, 'verificarStock']);
    Route::get('/historial', [PedidoController::class, 'historial'])->name('client.historial');
    Route::get('/pago/exito', [PagoController::class, 'exito'])->name('pago.exito');
    Route::post('/cambio-producto/solicitar', [CambioProductoController::class, 'solicitar'])->name('cambio.solicitar');
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
    Route::prefix('productos')->controller(ProductoController::class)->group(function () {
        Route::get('/', 'index')->name('admin.productosAdmin');
        Route::post('/crear', 'store')->name('productos.store');
        Route::put('/{codigo}', 'update')->name('productos.update');
        Route::delete('/{codigo}', 'destroy')->name('productos.destroy');
    });

    // Variantes
    Route::controller(VarianteController::class)->group(function () {
        Route::put('variantes/{variante}/actualizar', 'actualizarCantidad')->name('variantes.actualizar');
        Route::get('variantes/buscar', 'buscar')->name('admin.variantes.buscar');
    });

    // Pedidos
    Route::prefix('pedidos')->controller(PedidoController::class)->group(function () {
        Route::get('/', 'index')->name('admin.pedidosAdmin');
        Route::get('/buscar', 'buscarPorFiltros')->name('admin.pedidos.buscar');
        Route::get('/{id}', 'detalle')->name('admin.pedido.detalle');
        Route::put('/{id}/estado', 'cambiarEstado')->name('admin.pedido.cambiarEstado');
        Route::put('/{id}/cancelar', 'cancelar')->name('admin.pedidos.cancelar');
        Route::get('/{id}/imprimir', 'imprimir')->name('admin.pedidos.imprimir');
    });

    // Reclamos / Cambios
    Route::prefix('cambios')->controller(CambioProductoController::class)->group(function () {
        Route::get('/', 'index')->name('admin.cambios.index');
        Route::put('/{id}/procesar', 'procesar')->name('admin.cambios.procesar');
    });
});
