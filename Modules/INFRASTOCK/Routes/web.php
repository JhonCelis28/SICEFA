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

// Ruta para la lógica posterior al inicio de sesión del módulo.
Route::get('/postlogin','INFRASTOCKController@postlogin')->name('INFRASTOCK.postlogin');

/**
 * Rutas para la gestión de usuarios (Solo para administradores).
 * Estas rutas requieren autenticación y permisos de administrador.
 */
Route::middleware(['web', 'auth'])->prefix('infrastock/admin')->group(function () {
    // Dashboard del administrador
    Route::get('/dashboard','INFRASTOCKController@dashboard')->name('cefa.infrastock.admin.dashboard');
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
});

/**
 * Grupo de rutas para el Administrador con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth'])->group(function () {
    // Gestión de solicitudes del administrador
    Route::get('/infrastock/admin/requests', 'AdminRequestController@index')->name('infrastock.admin.requests.index');
    Route::post('/infrastock/admin/requests/{id}/approve', 'AdminRequestController@approve')->name('infrastock.admin.requests.approve');
    Route::post('/infrastock/admin/requests/{id}/reject', 'AdminRequestController@reject')->name('infrastock.admin.requests.reject');
    
    // Gestión de notificaciones
    Route::post('/infrastock/notifications/{id}/mark-read', 'NotificationController@markAsRead')->name('infrastock.notifications.mark-read');
    
    // Ruta de prueba para crear notificaciones
    Route::get('/infrastock/test-notification', function() {
        // Buscar usuarios con roles de administrador
        $adminRoleIds = [1, 5, 7, 16, 19, 24, 30, 38]; // IDs de roles de administrador
        $admins = \App\Models\User::whereHas('roles', function($query) use ($adminRoleIds) {
            $query->whereIn('roles.id', $adminRoleIds);
        })->get();

        // Si no hay administradores específicos, usar usuarios con rol "Administrador" o "Super Administrador"
        if ($admins->isEmpty()) {
            $admins = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'Administrador')
                      ->orWhere('name', 'Super Administrador');
            })->get();
        }

        // Si aún no hay administradores, usar el usuario actual como fallback
        if ($admins->isEmpty()) {
            $admins = collect([auth()->user()]);
        }

        $notificationCount = 0;
        foreach ($admins as $admin) {
            $notification = \Modules\INFRASTOCK\Entities\Notification::create([
                'type' => 'request_created',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $admin->id,
                'data' => [
                    'title' => 'Notificación de Prueba',
                    'message' => 'Esta es una notificación de prueba para verificar que el sistema funciona correctamente.',
                    'request_id' => 999,
                    'total_items' => 1,
                    'equipment_list' => 'Prueba',
                    'user_name' => 'Usuario de Prueba',
                    'action_url' => route('infrastock.admin.requests.index'),
                    'created_at' => now()->format('d/m/Y H:i'),
                ],
            ]);
            $notificationCount++;
        }
        
        return redirect()->route('infrastock.admin.requests.index')
            ->with('success', "Notificaciones de prueba creadas para {$notificationCount} administradores");
    })->name('infrastock.test.notification');
});

/**
 * Grupo de rutas para el Personal de Aseo con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal del personal de aseo
    Route::get('/infrastock/cleaning-staff/dashboard', 'CleaningStaffController@dashboard')->name('infrastock.cleaning-staff.dashboard');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/cleaning-staff/requests/create', 'CleaningStaffController@createRequest')->name('infrastock.cleaning-staff.requests.create');
    Route::post('/infrastock/cleaning-staff/requests', 'CleaningStaffController@storeRequest')->name('infrastock.cleaning-staff.requests.store');
    Route::get('/infrastock/cleaning-staff/requests', 'CleaningStaffController@myRequests')->name('infrastock.cleaning-staff.requests.index');
    
    // Gestión de sobrantes
    Route::get('/infrastock/cleaning-staff/surplus', 'SurplusController@index')->name('infrastock.cleaning-staff.surplus.index');
    Route::post('/infrastock/cleaning-staff/surplus', 'SurplusController@store')->name('infrastock.cleaning-staff.surplus.store');
    Route::get('/infrastock/cleaning-staff/surplus/{id}', 'SurplusController@show')->name('infrastock.cleaning-staff.surplus.show');
    Route::delete('/infrastock/cleaning-staff/surplus/{id}', 'SurplusController@destroy')->name('infrastock.cleaning-staff.surplus.destroy');
    
    // Acciones específicas para solicitudes
    Route::get('/infrastock/cleaning-staff/requests/{id}', 'CleaningStaffController@showRequest')->name('infrastock.cleaning-staff.requests.show');
    Route::get('/infrastock/cleaning-staff/requests/{id}/edit', 'CleaningStaffController@editRequest')->name('infrastock.cleaning-staff.requests.edit');
    Route::put('/infrastock/cleaning-staff/requests/{id}', 'CleaningStaffController@updateRequest')->name('infrastock.cleaning-staff.requests.update');
    Route::delete('/infrastock/cleaning-staff/requests/{id}', 'CleaningStaffController@destroyRequest')->name('infrastock.cleaning-staff.requests.destroy');
    
    // Notificaciones
    Route::get('/infrastock/cleaning-staff/notifications', 'CleaningStaffController@notifications')->name('infrastock.cleaning-staff.notifications');
    
    // Gestión de perfil
    Route::get('/infrastock/cleaning-staff/profile', 'CleaningStaffController@profile')->name('infrastock.cleaning-staff.profile');
    Route::put('/infrastock/cleaning-staff/profile', 'CleaningStaffController@updateProfile')->name('infrastock.cleaning-staff.profile.update');
});

/**
 * Grupo de rutas para el Operario con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal del operario
    Route::get('/infrastock/operator/dashboard', 'OperatorController@dashboard')->name('infrastock.operator.dashboard');
    
    // Visualización de stock en tiempo real
    Route::get('/infrastock/operator/stock', 'OperatorController@stock')->name('infrastock.operator.stock');
    Route::get('/infrastock/operator/equipment/{id}', 'OperatorController@showEquipment')->name('infrastock.operator.equipment.show');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/operator/requests/create', 'OperatorController@createRequest')->name('infrastock.operator.requests.create');
    Route::post('/infrastock/operator/requests', 'OperatorController@storeRequest')->name('infrastock.operator.requests.store');
    Route::get('/infrastock/operator/requests', 'OperatorController@myRequests')->name('infrastock.operator.requests.index');
    Route::get('/infrastock/operator/requests/{id}', 'OperatorController@showRequest')->name('infrastock.operator.requests.show');
    
    // Notificaciones
    Route::get('/infrastock/operator/notifications', 'OperatorController@notifications')->name('infrastock.operator.notifications');
    Route::post('/infrastock/operator/notifications/{id}/mark-read', 'OperatorController@markNotificationAsRead')->name('infrastock.operator.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/operator/profile', 'OperatorController@profile')->name('infrastock.operator.profile');
    Route::put('/infrastock/operator/profile', 'OperatorController@updateProfile')->name('infrastock.operator.profile.update');
    
    // Reporte de sobrantes
    Route::get('/infrastock/operator/surplus-report', 'OperatorController@surplusReport')->name('infrastock.operator.surplus-report');
    Route::post('/infrastock/operator/surplus', 'OperatorController@storeSurplus')->name('infrastock.operator.surplus.store');
    Route::get('/infrastock/operator/surplus/{id}', 'OperatorController@showSurplus')->name('infrastock.operator.surplus.show');
    Route::delete('/infrastock/operator/surplus/{id}', 'OperatorController@destroySurplus')->name('infrastock.operator.surplus.destroy');
    
    // Logout del operario
    Route::post('/infrastock/operator/logout', 'OperatorController@logout')->name('infrastock.operator.logout');
});

/**
 * Grupo de rutas para el Centro de Convivencia con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal del centro de convivencia
    Route::get('/infrastock/convivencia/dashboard', 'ConvivenciaController@dashboard')->name('infrastock.convivencia.dashboard');
    
    // Visualización de stock en tiempo real
    Route::get('/infrastock/convivencia/stock', 'ConvivenciaController@stock')->name('infrastock.convivencia.stock');
    Route::get('/infrastock/convivencia/equipment/{id}', 'ConvivenciaController@showEquipment')->name('infrastock.convivencia.equipment.show');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/convivencia/requests/create', 'ConvivenciaController@createRequest')->name('infrastock.convivencia.requests.create');
    Route::post('/infrastock/convivencia/requests', 'ConvivenciaController@storeRequest')->name('infrastock.convivencia.requests.store');
    Route::get('/infrastock/convivencia/requests', 'ConvivenciaController@myRequests')->name('infrastock.convivencia.requests.index');
    Route::get('/infrastock/convivencia/requests/{id}', 'ConvivenciaController@showRequest')->name('infrastock.convivencia.requests.show');
    
    // Notificaciones
    Route::get('/infrastock/convivencia/notifications', 'ConvivenciaController@notifications')->name('infrastock.convivencia.notifications');
    Route::post('/infrastock/convivencia/notifications/{id}/mark-read', 'ConvivenciaController@markNotificationAsRead')->name('infrastock.convivencia.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/convivencia/profile', 'ConvivenciaController@profile')->name('infrastock.convivencia.profile');
    Route::put('/infrastock/convivencia/profile', 'ConvivenciaController@updateProfile')->name('infrastock.convivencia.profile.update');
    
    // Reporte de sobrantes
    Route::get('/infrastock/convivencia/surplus-report', 'ConvivenciaController@surplusReport')->name('infrastock.convivencia.surplus-report');
    Route::post('/infrastock/convivencia/surplus', 'ConvivenciaController@storeSurplus')->name('infrastock.convivencia.surplus.store');
    Route::get('/infrastock/convivencia/surplus/{id}', 'ConvivenciaController@showSurplus')->name('infrastock.convivencia.surplus.show');
    Route::delete('/infrastock/convivencia/surplus/{id}', 'ConvivenciaController@destroySurplus')->name('infrastock.convivencia.surplus.destroy');
    
    // Logout del centro de convivencia
    Route::post('/infrastock/convivencia/logout', 'ConvivenciaController@logout')->name('infrastock.convivencia.logout');
});

/**
 * Grupo de rutas para Ganadería con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal de ganadería
    Route::get('/infrastock/ganaderia/dashboard', 'GanaderiaController@dashboard')->name('infrastock.ganaderia.dashboard');
    
    // Visualización de stock en tiempo real
    Route::get('/infrastock/ganaderia/stock', 'GanaderiaController@stock')->name('infrastock.ganaderia.stock');
    Route::get('/infrastock/ganaderia/equipment/{id}', 'GanaderiaController@showEquipment')->name('infrastock.ganaderia.equipment.show');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/ganaderia/requests/create', 'GanaderiaController@createRequest')->name('infrastock.ganaderia.requests.create');
    Route::post('/infrastock/ganaderia/requests', 'GanaderiaController@storeRequest')->name('infrastock.ganaderia.requests.store');
    Route::get('/infrastock/ganaderia/requests', 'GanaderiaController@myRequests')->name('infrastock.ganaderia.requests.index');
    Route::get('/infrastock/ganaderia/requests/{id}', 'GanaderiaController@showRequest')->name('infrastock.ganaderia.requests.show');
    
    // Notificaciones
    Route::get('/infrastock/ganaderia/notifications', 'GanaderiaController@notifications')->name('infrastock.ganaderia.notifications');
    Route::post('/infrastock/ganaderia/notifications/{id}/mark-read', 'GanaderiaController@markNotificationAsRead')->name('infrastock.ganaderia.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/ganaderia/profile', 'GanaderiaController@profile')->name('infrastock.ganaderia.profile');
    Route::put('/infrastock/ganaderia/profile', 'GanaderiaController@updateProfile')->name('infrastock.ganaderia.profile.update');
    
    // Reporte de sobrantes
    Route::get('/infrastock/ganaderia/surplus-report', 'GanaderiaController@surplusReport')->name('infrastock.ganaderia.surplus-report');
    Route::post('/infrastock/ganaderia/surplus', 'GanaderiaController@storeSurplus')->name('infrastock.ganaderia.surplus.store');
    Route::get('/infrastock/ganaderia/surplus/{id}', 'GanaderiaController@showSurplus')->name('infrastock.ganaderia.surplus.show');
    Route::delete('/infrastock/ganaderia/surplus/{id}', 'GanaderiaController@destroySurplus')->name('infrastock.ganaderia.surplus.destroy');
    
    // Logout de ganadería
    Route::post('/infrastock/ganaderia/logout', 'GanaderiaController@logout')->name('infrastock.ganaderia.logout');
});