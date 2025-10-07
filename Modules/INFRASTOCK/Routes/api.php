<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas API para tu aplicación.
| Estas rutas son cargadas por el RouteServiceProvider dentro de un grupo
| que tiene asignado el grupo de middleware "api". ¡Disfruta construyendo tu API!
|
| Todas las rutas definidas aquí pertenecen al módulo INFRASTOCK.
*/

/**
 * Ruta de ejemplo para el API del módulo INFRASTOCK.
 * Retorna los datos del usuario autenticado a través del guard 'api'.
 * Requiere autenticación API.
 *
 * @param \Illuminate\Http\Request $request La solicitud HTTP.
 * @return \App\Models\User Los datos del usuario autenticado.
 */
Route::middleware('auth:api')->get('/infrastock', function (Request $request) {
    return $request->user();
});