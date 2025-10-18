<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| Rutas de API
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas de API para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro de un grupo que
| contiene el middleware "api". ¡Disfruta construyendo tu API!
|
*/

// Rutas de autenticación públicas (no requieren token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas de recursos para productos (completamente públicas)
// Ahora todas las operaciones (index, store, show, update, destroy)
// están disponibles sin autenticación.
Route::apiResource('products', ProductController::class);

// Rutas protegidas (requieren un token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Ruta para obtener la información del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Ruta para cerrar la sesión del usuario
    Route::post('/logout', [AuthController::class, 'logout']);
});
