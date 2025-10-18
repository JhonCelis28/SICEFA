<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas web para tu aplicación.
| Estas rutas son cargadas por el RouteServiceProvider dentro de un grupo
| que contiene el grupo de middleware "web".
|
| Todas las rutas definidas aquí pertenecen al módulo INFRASTOCK y están
| prefijadas con '/infrastock' y generalmente protegidas por el middleware 'auth'.
*/

// Ruta principal del módulo INFRASTOCK, que redirige a la página de inicio del módulo.
Route::get('/infrastock', 'INFRASTOCKController@index')->name('cefa.infrastock.index');

// Ruta para manejar la redirección después del login desde SICA
Route::get('/infrastock/post-login', 'INFRASTOCKController@postlogin')->name('infrastock.post-login');

/**
 * Grupo de rutas para la administración de Áreas Productivas.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Muestra todas las áreas productivas.
    Route::get('/infrastock/admin/areas', 'AreaController@index')->name('infrastock.admin.areas.index');
    // Muestra el formulario para crear una nueva área (gestionado vía modal).
    Route::get('/infrastock/admin/areas/create', 'AreaController@create')->name('infrastock.admin.areas.create');
    // Almacena una nueva área productiva.
    Route::post('/infrastock/admin/areas', 'AreaController@store')->name('infrastock.admin.areas.store');
    // Muestra los detalles de un área específica (gestionado vía modal).
    Route::get('/infrastock/admin/areas/{area}', 'AreaController@show')->name('infrastock.admin.areas.show');
    // Muestra el formulario para editar un área específica (gestionado vía modal).
    Route::get('/infrastock/admin/areas/{area}/edit', 'AreaController@edit')->name('infrastock.admin.areas.edit');
    // Actualiza un área productiva existente.
    Route::put('/infrastock/admin/areas/{area}', 'AreaController@update')->name('infrastock.admin.areas.update');
    // Elimina un área productiva.
    Route::delete('/infrastock/admin/areas/{area}', 'AreaController@destroy')->name('infrastock.admin.areas.destroy');
});

/**
 * Grupo de rutas para la administración de Categorías.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Muestra todas las categorías.
    Route::get('/infrastock/admin/categories', 'CategoryController@index')->name('infrastock.admin.categories.index');
    // Muestra el formulario para crear una nueva categoría (gestionado vía modal).
    Route::get('/infrastock/admin/categories/create', 'CategoryController@create')->name('infrastock.admin.categories.create');
    // Almacena una nueva categoría.
    Route::post('/infrastock/admin/categories', 'CategoryController@store')->name('infrastock.admin.categories.store');
    // Muestra los detalles de una categoría específica (gestionado vía modal).
    Route::get('/infrastock/admin/categories/{category}', 'CategoryController@show')->name('infrastock.admin.categories.show');
    // Muestra el formulario para editar una categoría específica (gestionado vía modal).
    Route::get('/infrastock/admin/categories/{category}/edit', 'CategoryController@edit')->name('infrastock.admin.categories.edit');
    // Actualiza una categoría existente.
    Route::put('/infrastock/admin/categories/{category}', 'CategoryController@update')->name('infrastock.admin.categories.update');
    // Elimina una categoría.
    Route::delete('/infrastock/admin/categories/{category}', 'CategoryController@destroy')->name('infrastock.admin.categories.destroy');
});

/**
 * Grupo de rutas para la administración de Insumos.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Muestra todos los insumos.
    Route::get('/infrastock/admin/supplies', 'SupplyController@index')->name('infrastock.admin.supplies.index');
    // Muestra el formulario para crear un nuevo insumo (gestionado vía modal).
    Route::get('/infrastock/admin/supplies/create', 'SupplyController@create')->name('infrastock.admin.supplies.create');
    // Almacena un nuevo insumo.
    Route::post('/infrastock/admin/supplies', 'SupplyController@store')->name('infrastock.admin.supplies.store');
    // Muestra los detalles de un insumo específico.
    Route::get('/infrastock/admin/supplies/{supply}', 'SupplyController@show')->name('infrastock.admin.supplies.show');
    // Muestra el formulario para editar un insumo específico (gestionado vía modal).
    Route::get('/infrastock/admin/supplies/{supply}/edit', 'SupplyController@edit')->name('infrastock.admin.supplies.edit');
    // Actualiza un insumo existente.
    Route::put('/infrastock/admin/supplies/{supply}', 'SupplyController@update')->name('infrastock.admin.supplies.update');
    // Elimina un insumo.
    Route::delete('/infrastock/admin/supplies/{supply}', 'SupplyController@destroy')->name('infrastock.admin.supplies.destroy');
});

/**
 * Grupo de rutas para la administración de Herramientas.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Muestra todas las herramientas.
    Route::get('/infrastock/admin/tools', 'ToolController@index')->name('infrastock.admin.tools.index');
    // Muestra el formulario para crear una nueva herramienta (gestionado vía modal).
    Route::get('/infrastock/admin/tools/create', 'ToolController@create')->name('infrastock.admin.tools.create');
    // Almacena una nueva herramienta.
    Route::post('/infrastock/admin/tools', 'ToolController@store')->name('infrastock.admin.tools.store');
    // Muestra los detalles de una herramienta específica.
    Route::get('/infrastock/admin/tools/{tool}', 'ToolController@show')->name('infrastock.admin.tools.show');
    // Muestra el formulario para editar una herramienta específica (gestionado vía modal).
    Route::get('/infrastock/admin/tools/{tool}/edit', 'ToolController@edit')->name('infrastock.admin.tools.edit');
    // Actualiza una herramienta existente.
    Route::put('/infrastock/admin/tools/{tool}', 'ToolController@update')->name('infrastock.admin.tools.update');
    // Elimina una herramienta.
    Route::delete('/infrastock/admin/tools/{tool}', 'ToolController@destroy')->name('infrastock.admin.tools.destroy');
});

/**
 * Grupo de rutas para la gestión de Préstamos y Devoluciones.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Muestra todos los préstamos y devoluciones.
    Route::get('/infrastock/admin/loans', 'LoanController@index')->name('infrastock.admin.loans.index');
    // Muestra el formulario para registrar un nuevo movimiento (gestionado vía modal).
    Route::get('/infrastock/admin/loans/create', 'LoanController@create')->name('infrastock.admin.loans.create');
    // Almacena un nuevo movimiento de préstamo o devolución.
    Route::post('/infrastock/admin/loans', 'LoanController@store')->name('infrastock.admin.loans.store');
    // Muestra los detalles de un movimiento específico (gestionado vía modal).
    Route::get('/infrastock/admin/loans/{loan}', 'LoanController@show')->name('infrastock.admin.loans.show');
    // Muestra el formulario para editar un movimiento específico (gestionado vía modal).
    Route::get('/infrastock/admin/loans/{loan}/edit', 'LoanController@edit')->name('infrastock.admin.loans.edit');
    // Actualiza un movimiento existente.
    Route::put('/infrastock/admin/loans/{loan}', 'LoanController@update')->name('infrastock.admin.loans.update');
    // Elimina un movimiento.
    Route::delete('/infrastock/admin/loans/{loan}', 'LoanController@destroy')->name('infrastock.admin.loans.destroy');
});

/**
 * Grupo de rutas para la gestión de Solicitudes de Insumos.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Muestra todas las solicitudes de insumos.
    Route::get('/infrastock/admin/supply-requests', 'SupplyRequestController@index')->name('infrastock.admin.supply-requests.index');
    // Muestra el formulario para crear una nueva solicitud (gestionado vía modal).
    Route::get('/infrastock/admin/supply-requests/create', 'SupplyRequestController@create')->name('infrastock.admin.supply-requests.create');
    // Almacena una nueva solicitud de insumo.
    Route::post('/infrastock/admin/supply-requests', 'SupplyRequestController@store')->name('infrastock.admin.supply-requests.store');
    // Muestra los detalles de una solicitud específica (gestionado vía modal).
    Route::get('/infrastock/admin/supply-requests/{supply_request}', 'SupplyRequestController@show')->name('infrastock.admin.supply-requests.show');
    // Muestra el formulario para editar una solicitud específica (gestionado vía modal).
    Route::get('/infrastock/admin/supply-requests/{supply_request}/edit', 'SupplyRequestController@edit')->name('infrastock.admin.supply-requests.edit');
    // Actualiza una solicitud de insumo existente.
    Route::put('/infrastock/admin/supply-requests/{supply_request}', 'SupplyRequestController@update')->name('infrastock.admin.supply-requests.update');
    // Elimina una solicitud de insumo.
    Route::delete('/infrastock/admin/supply-requests/{supply_request}', 'SupplyRequestController@destroy')->name('infrastock.admin.supply-requests.destroy');
});

/**
 * Rutas para el Perfil de Usuario.
 * Requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Muestra el formulario para editar el perfil del usuario autenticado.
    Route::get('/infrastock/admin/profile/edit', 'UserProfileController@edit')->name('cefa.infrastock.admin.profile.edit');
});

// Ruta para acceder al logo del módulo (si se requiere directamente).
Route::get('/logo', function() {
    $path = public_path('assets/img/logo.png');
    return response()->file($path);
});

// Ruta para el dashboard del administrador del módulo INFRASTOCK.
Route::get('/infrastock/admin/dashboard','INFRASTOCKController@dashboard')->name('cefa.infrastock.admin.dashboard');

// Ruta para la lógica posterior al inicio de sesión del módulo.
Route::get('/postlogin','INFRASTOCKController@postlogin')->name('INFRASTOCK.postlogin');

/**
 * Rutas para la gestión de usuarios (Solo para administradores).
 * Estas rutas requieren autenticación y permisos de administrador.
 */
Route::middleware(['web', 'auth'])->prefix('infrastock/admin')->group(function () {
    // Gestión de usuarios
    Route::resource('users', 'UserManagementController')->names([
        'index' => 'infrastock.admin.users.index',
        'create' => 'infrastock.admin.users.create',
        'store' => 'infrastock.admin.users.store',
        'show' => 'infrastock.admin.users.show',
        'edit' => 'infrastock.admin.users.edit',
        'update' => 'infrastock.admin.users.update',
        'destroy' => 'infrastock.admin.users.destroy',
    ]);
    
            // Cambiar estado de usuario (AJAX)
            Route::post('users/{id}/toggle-status', 'UserManagementController@toggleStatus')->name('infrastock.admin.users.toggle-status');
            
            // Verificar solicitudes pendientes de usuario (AJAX)
            Route::get('users/{id}/check-requests', 'UserManagementController@checkRequests')->name('infrastock.admin.users.check-requests');
});

/**
 * Rutas públicas para el Personal de Aseo (registro y login).
 * Estas rutas no requieren autenticación previa.
 */
Route::prefix('infrastock/cleaning-staff')->group(function () {
    // Registro del personal de aseo
    Route::get('/register', 'CleaningStaffController@showRegistrationForm')->name('infrastock.cleaning-staff.register');
    Route::post('/register', 'CleaningStaffController@register')->name('infrastock.cleaning-staff.register.post');
    
    // Login del personal de aseo
    Route::get('/login', 'CleaningStaffController@showLoginForm')->name('infrastock.cleaning-staff.login');
    Route::post('/login', 'CleaningStaffController@login')->name('infrastock.cleaning-staff.login.post');
});

/**
 * Grupo de rutas para el Personal de Aseo.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Logout del administrador
    Route::post('/infrastock/admin/logout', 'INFRASTOCKController@logout')->name('infrastock.admin.logout');
    
    // Logout del personal de aseo
    Route::post('/infrastock/cleaning-staff/logout', 'CleaningStaffController@logout')->name('infrastock.cleaning-staff.logout');
    
    // Dashboard principal del personal de aseo
    Route::get('/infrastock/cleaning-staff/dashboard', 'CleaningStaffController@dashboard')->name('infrastock.cleaning-staff.dashboard');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/cleaning-staff/requests/create', 'CleaningStaffController@createRequest')->name('infrastock.cleaning-staff.requests.create');
    Route::post('/infrastock/cleaning-staff/requests', 'CleaningStaffController@storeRequest')->name('infrastock.cleaning-staff.requests.store');
    Route::get('/infrastock/cleaning-staff/requests', 'CleaningStaffController@myRequests')->name('infrastock.cleaning-staff.requests.index');
    
    // Notificaciones
    Route::get('/infrastock/cleaning-staff/notifications', 'CleaningStaffController@notifications')->name('infrastock.cleaning-staff.notifications');
    
    // Gestión de perfil
    Route::get('/infrastock/cleaning-staff/profile', 'CleaningStaffController@profile')->name('infrastock.cleaning-staff.profile');
    Route::put('/infrastock/cleaning-staff/profile', 'CleaningStaffController@updateProfile')->name('infrastock.cleaning-staff.profile.update');
    
    // Reportes de sobrantes (solo filtros, sin exportación)
    Route::get('/infrastock/cleaning-staff/surplus-report', 'CleaningStaffController@surplusReport')->name('infrastock.cleaning-staff.surplus-report');
    
    // Historial de insumos
    Route::get('/infrastock/cleaning-staff/supply-history', 'CleaningStaffController@supplyHistory')->name('infrastock.cleaning-staff.supply-history');
});