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

// Ruta para la página de desarrolladores
Route::get('/infrastock/developers', 'INFRASTOCKController@developers')->name('cefa.infrastock.developers');

// Ruta para manejar la redirección después del login desde SICA
// Solo redirige, no hace nada más (igual que el admin)
Route::get('/infrastock/post-login', 'INFRASTOCKController@postlogin')->name('infrastock.post-login')->middleware('auth');

/**
 * Grupo de rutas para la administración de Áreas Productivas.
 * Todas estas rutas requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
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
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
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
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
    // Muestra todos los insumos.
    Route::get('/infrastock/admin/supplies', 'SupplyController@index')->name('infrastock.admin.supplies.index');
    // Muestra el formulario para crear un nuevo insumo (gestionado vía modal).
    Route::get('/infrastock/admin/supplies/create', 'SupplyController@create')->name('infrastock.admin.supplies.create');
    // Almacena un nuevo insumo.
    Route::post('/infrastock/admin/supplies', 'SupplyController@store')->name('infrastock.admin.supplies.store');
    // Almacena un nuevo préstamo de insumo desde el perfil del admin.
    Route::post('/infrastock/admin/supplies/loan', 'SupplyController@storeLoan')->name('infrastock.admin.supplies.store-loan');
    // Muestra todos los préstamos de insumos registrados por el admin (debe ir ANTES de la ruta con parámetro).
    Route::get('/infrastock/admin/supplies/loans', 'SupplyController@indexLoans')->name('infrastock.admin.supplies.loans.index');
    // Procesa la devolución de un préstamo de insumo.
    Route::post('/infrastock/admin/supplies/loans/{id}/return', 'SupplyController@returnLoan')->name('infrastock.admin.supplies.loans.return');
    // Exportar insumos a PDF.
    Route::get('/infrastock/admin/supplies/export/pdf', 'SupplyController@exportPdf')->name('infrastock.admin.supplies.export.pdf');
    // Exportar insumos a Excel.
    Route::get('/infrastock/admin/supplies/export/excel', 'SupplyController@exportExcel')->name('infrastock.admin.supplies.export.excel');
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
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
    // Muestra todas las herramientas.
    Route::get('/infrastock/admin/tools', 'ToolController@index')->name('infrastock.admin.tools.index');
    // Muestra el formulario para crear una nueva herramienta (gestionado vía modal).
    Route::get('/infrastock/admin/tools/create', 'ToolController@create')->name('infrastock.admin.tools.create');
    // Almacena una nueva herramienta.
    Route::post('/infrastock/admin/tools', 'ToolController@store')->name('infrastock.admin.tools.store');
    // Exportar herramientas a PDF.
    Route::get('/infrastock/admin/tools/export/pdf', 'ToolController@exportPdf')->name('infrastock.admin.tools.export.pdf');
    // Exportar herramientas a Excel.
    Route::get('/infrastock/admin/tools/export/excel', 'ToolController@exportExcel')->name('infrastock.admin.tools.export.excel');
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
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
    // Muestra todos los préstamos y devoluciones.
    Route::get('/infrastock/admin/loans', 'LoanController@index')->name('infrastock.admin.loans.index');
    // Muestra el formulario para registrar un nuevo movimiento (gestionado vía modal).
    Route::get('/infrastock/admin/loans/create', 'LoanController@create')->name('infrastock.admin.loans.create');
    // Almacena un nuevo movimiento de préstamo o devolución.
    Route::post('/infrastock/admin/loans', 'LoanController@store')->name('infrastock.admin.loans.store');
    // Exportar préstamos a PDF (DEBE ir ANTES de las rutas con parámetro {loan}).
    Route::get('/infrastock/admin/loans/export/pdf', 'LoanController@exportPdf')->name('infrastock.admin.loans.export.pdf');
    // Exportar préstamos a Excel (DEBE ir ANTES de las rutas con parámetro {loan}).
    Route::get('/infrastock/admin/loans/export/excel', 'LoanController@exportExcel')->name('infrastock.admin.loans.export.excel');
    // Muestra los detalles de un movimiento específico (gestionado vía modal).
    Route::get('/infrastock/admin/loans/{loan}', 'LoanController@show')->name('infrastock.admin.loans.show');
    // Muestra el formulario para editar un movimiento específico (gestionado vía modal).
    Route::get('/infrastock/admin/loans/{loan}/edit', 'LoanController@edit')->name('infrastock.admin.loans.edit');
    // Actualiza un movimiento existente.
    Route::put('/infrastock/admin/loans/{loan}', 'LoanController@update')->name('infrastock.admin.loans.update');
    // Elimina un movimiento.
    Route::delete('/infrastock/admin/loans/{loan}', 'LoanController@destroy')->name('infrastock.admin.loans.destroy');
    // Registra la devolución de un préstamo (cambia a Devolución, restaura stock, bloquea registro).
    Route::post('/infrastock/admin/loans/{id}/return', 'LoanController@returnLoan')->name('infrastock.admin.loans.return');
    // Aprueba una devolución de insumos.
    Route::post('/infrastock/admin/loans/{id}/approve-return', 'LoanController@approveReturn')->name('infrastock.admin.loans.approve-return');
    // Rechaza una devolución de insumos.
    Route::post('/infrastock/admin/loans/{id}/reject-return', 'LoanController@rejectReturn')->name('infrastock.admin.loans.reject-return');
    // Aprueba un préstamo de herramienta.
    Route::post('/infrastock/admin/loans/{id}/approve-loan', 'LoanController@approveLoan')->name('infrastock.admin.loans.approve-loan');
    // Rechaza un préstamo de herramienta.
    Route::post('/infrastock/admin/loans/{id}/reject-loan', 'LoanController@rejectLoan')->name('infrastock.admin.loans.reject-loan');
});

/**
 * Grupo de rutas para la gestión de Solicitudes de Insumos y Devoluciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
    // Muestra todas las solicitudes de insumos.
    Route::get('/infrastock/admin/supply-requests', 'SupplyRequestController@index')->name('infrastock.admin.supply-requests.index');
    // Muestra el formulario para crear una nueva solicitud (gestionado vía modal).
    Route::get('/infrastock/admin/supply-requests/create', 'SupplyRequestController@create')->name('infrastock.admin.supply-requests.create');
    // Almacena una nueva solicitud de insumo.
    Route::post('/infrastock/admin/supply-requests', 'SupplyRequestController@store')->name('infrastock.admin.supply-requests.store');
    // Exportar consumos a PDF.
    Route::get('/infrastock/admin/supply-requests/export/pdf', 'SupplyRequestController@exportPdf')->name('infrastock.admin.supply-requests.export.pdf');
    // Exportar consumos a Excel.
    Route::get('/infrastock/admin/supply-requests/export/excel', 'SupplyRequestController@exportExcel')->name('infrastock.admin.supply-requests.export.excel');
    // Muestra los detalles de una solicitud específica (gestionado vía modal).
    Route::get('/infrastock/admin/supply-requests/{supply_request}', 'SupplyRequestController@show')->name('infrastock.admin.supply-requests.show');
    // Muestra el formulario para editar una solicitud específica (gestionado vía modal).
    Route::get('/infrastock/admin/supply-requests/{supply_request}/edit', 'SupplyRequestController@edit')->name('infrastock.admin.supply-requests.edit');
    // Actualiza una solicitud de insumo existente.
    Route::put('/infrastock/admin/supply-requests/{supply_request}', 'SupplyRequestController@update')->name('infrastock.admin.supply-requests.update');
    // Elimina una solicitud de insumo.
    Route::delete('/infrastock/admin/supply-requests/{supply_request}', 'SupplyRequestController@destroy')->name('infrastock.admin.supply-requests.destroy');
    
    // Devoluciones de insumos
    Route::get('/infrastock/admin/supply-returns', 'SupplyReturnController@index')->name('infrastock.admin.supply-returns.index');
    Route::post('/infrastock/admin/supply-returns/{id}/approve', 'SupplyReturnController@approve')->name('infrastock.admin.supply-returns.approve');
    Route::post('/infrastock/admin/supply-returns/{id}/reject', 'SupplyReturnController@reject')->name('infrastock.admin.supply-returns.reject');
});

/**
 * Rutas para el Perfil de Usuario.
 * Requieren que el usuario esté autenticado.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
    // Muestra el formulario para editar el perfil del usuario autenticado.
    Route::get('/infrastock/admin/profile/edit', 'UserProfileController@edit')->name('cefa.infrastock.admin.profile.edit');
    // Actualiza el perfil del usuario autenticado.
    Route::put('/infrastock/admin/profile', 'UserProfileController@update')->name('cefa.infrastock.admin.profile.update');
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
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->prefix('infrastock/admin')->group(function () {
    // Dashboard del administrador
    Route::get('/dashboard','INFRASTOCKController@dashboard')->name('cefa.infrastock.admin.dashboard');
    
    // Reportes
    Route::get('/reports/consumption-by-area', 'ReportController@consumptionByArea')->name('infrastock.admin.reports.consumption-by-area');
    Route::get('/reports/consumption-by-area/pdf', 'ReportController@consumptionByAreaPdf')->name('infrastock.admin.reports.consumption-by-area.pdf');
    Route::get('/reports/tools-by-instructor', 'ReportController@toolsByInstructor')->name('infrastock.admin.reports.tools-by-instructor');
    Route::get('/reports/tools-by-instructor/pdf', 'ReportController@toolsByInstructorPdf')->name('infrastock.admin.reports.tools-by-instructor.pdf');
    
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
 * Ruta compartida para marcar notificaciones como leídas.
 * Accesible por TODOS los usuarios autenticados (admin, psicola, instructor, etc.)
 */
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/infrastock/notifications/{id}/mark-read', 'NotificationController@markAsRead')->name('infrastock.notifications.mark-read');
});

/**
 * Grupo de rutas para logout del administrador.
 * Estas rutas requieren autenticación para validar el CSRF token.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
    // Logout del administrador (debe ir ANTES de otras rutas con parámetros)
    Route::post('/infrastock/admin/logout', 'INFRASTOCKController@logout')->name('infrastock.admin.logout');
});

/**
 * Grupo de rutas para el Administrador con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class, \Modules\INFRASTOCK\Http\Middleware\VerifyAdminRole::class])->group(function () {
    // Gestión de solicitudes del administrador
    Route::get('/infrastock/admin/requests', 'AdminRequestController@index')->name('infrastock.admin.requests.index');
    Route::post('/infrastock/admin/requests/{id}/approve', 'AdminRequestController@approve')->name('infrastock.admin.requests.approve');
    Route::post('/infrastock/admin/requests/{id}/reject', 'AdminRequestController@reject')->name('infrastock.admin.requests.reject');
    
    // Ruta de prueba para crear notificaciones
    Route::get('/infrastock/test-notification', function() {
        // Buscar usuarios con roles de administrador de INFRASTOCK por slug o nombre
        $admins = \App\Models\User::whereHas('roles', function($query) {
            $query->where('slug', 'infrastock.admin')
                  ->orWhere('slug', 'superadmin')
                  ->orWhere('name', 'Administrador')
                  ->orWhere('name', 'Super Administrador');
        })->get();

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
                    'action_url' => route('infrastock.admin.supply-requests.index'),
                    'created_at' => now()->format('d/m/Y H:i'),
                ],
            ]);
            $notificationCount++;
        }
        
        return redirect()->route('infrastock.admin.supply-requests.index')
            ->with('success', "Notificaciones de prueba creadas para {$notificationCount} administradores");
    })->name('infrastock.test.notification');
});

/**
 * Grupo de rutas para el Personal de Aseo con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Logout del personal de aseo (debe ir ANTES de otras rutas con parámetros)
    Route::post('/infrastock/cleaning-staff/logout', 'CleaningStaffController@logout')->name('infrastock.cleaning-staff.logout');
    
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
    Route::put('/infrastock/cleaning-staff/surplus/{id}', 'SurplusController@update')->name('infrastock.cleaning-staff.surplus.update');
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
    Route::get('/infrastock/operator/requests/{id}/edit', 'OperatorController@editRequest')->name('infrastock.operator.requests.edit');
    Route::put('/infrastock/operator/requests/{id}', 'OperatorController@updateRequest')->name('infrastock.operator.requests.update');
    Route::delete('/infrastock/operator/requests/{id}', 'OperatorController@destroyRequest')->name('infrastock.operator.requests.destroy');
    
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
    Route::get('/infrastock/convivencia/requests/{id}/edit', 'ConvivenciaController@editRequest')->name('infrastock.convivencia.requests.edit');
    Route::put('/infrastock/convivencia/requests/{id}', 'ConvivenciaController@updateRequest')->name('infrastock.convivencia.requests.update');
    Route::delete('/infrastock/convivencia/requests/{id}', 'ConvivenciaController@destroyRequest')->name('infrastock.convivencia.requests.destroy');
    
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
    Route::get('/infrastock/ganaderia/requests/{id}/edit', 'GanaderiaController@editRequest')->name('infrastock.ganaderia.requests.edit');
    Route::put('/infrastock/ganaderia/requests/{id}', 'GanaderiaController@updateRequest')->name('infrastock.ganaderia.requests.update');
    Route::delete('/infrastock/ganaderia/requests/{id}', 'GanaderiaController@destroyRequest')->name('infrastock.ganaderia.requests.destroy');
    
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

/**
 * Grupo de rutas para Psicola con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal de Psicola
    Route::get('/infrastock/psicola/dashboard', 'PsicolaController@dashboard')->name('infrastock.psicola.dashboard');
    
    // Visualización de stock en tiempo real
    Route::get('/infrastock/psicola/stock', 'PsicolaController@stock')->name('infrastock.psicola.stock');
    Route::get('/infrastock/psicola/equipment/{id}', 'PsicolaController@showEquipment')->name('infrastock.psicola.equipment.show');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/psicola/requests/create', 'PsicolaController@createRequest')->name('infrastock.psicola.requests.create');
    Route::post('/infrastock/psicola/requests', 'PsicolaController@storeRequest')->name('infrastock.psicola.requests.store');
    Route::get('/infrastock/psicola/requests', 'PsicolaController@myRequests')->name('infrastock.psicola.requests.index');
    Route::get('/infrastock/psicola/requests/{id}', 'PsicolaController@showRequest')->name('infrastock.psicola.requests.show');
    
    // Notificaciones
    Route::get('/infrastock/psicola/notifications', 'PsicolaController@notifications')->name('infrastock.psicola.notifications');
    Route::post('/infrastock/psicola/notifications/{id}/mark-read', 'PsicolaController@markNotificationAsRead')->name('infrastock.psicola.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/psicola/profile', 'PsicolaController@profile')->name('infrastock.psicola.profile');
    Route::put('/infrastock/psicola/profile', 'PsicolaController@updateProfile')->name('infrastock.psicola.profile.update');
    
    // Reporte de sobrantes
    Route::get('/infrastock/psicola/surplus-report', 'PsicolaController@surplusReport')->name('infrastock.psicola.surplus-report');
    Route::post('/infrastock/psicola/surplus', 'PsicolaController@storeSurplus')->name('infrastock.psicola.surplus.store');
    Route::get('/infrastock/psicola/surplus/{id}', 'PsicolaController@showSurplus')->name('infrastock.psicola.surplus.show');
    Route::delete('/infrastock/psicola/surplus/{id}', 'PsicolaController@destroySurplus')->name('infrastock.psicola.surplus.destroy');
    
    // Logout de Psicola
    Route::post('/infrastock/psicola/logout', 'PsicolaController@logout')->name('infrastock.psicola.logout');
});

/**
 * Grupo de rutas para Ciencias Basicas con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal de Ciencias Basicas
    Route::get('/infrastock/ciencias-basicas/dashboard', 'CienciasBasicasController@dashboard')->name('infrastock.ciencias-basicas.dashboard');
    
    // Visualización de stock en tiempo real
    Route::get('/infrastock/ciencias-basicas/stock', 'CienciasBasicasController@stock')->name('infrastock.ciencias-basicas.stock');
    Route::get('/infrastock/ciencias-basicas/equipment/{id}', 'CienciasBasicasController@showEquipment')->name('infrastock.ciencias-basicas.equipment.show');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/ciencias-basicas/requests/create', 'CienciasBasicasController@createRequest')->name('infrastock.ciencias-basicas.requests.create');
    Route::post('/infrastock/ciencias-basicas/requests', 'CienciasBasicasController@storeRequest')->name('infrastock.ciencias-basicas.requests.store');
    Route::get('/infrastock/ciencias-basicas/requests', 'CienciasBasicasController@myRequests')->name('infrastock.ciencias-basicas.requests.index');
    Route::get('/infrastock/ciencias-basicas/requests/{id}', 'CienciasBasicasController@showRequest')->name('infrastock.ciencias-basicas.requests.show');
    
    // Notificaciones
    Route::get('/infrastock/ciencias-basicas/notifications', 'CienciasBasicasController@notifications')->name('infrastock.ciencias-basicas.notifications');
    Route::post('/infrastock/ciencias-basicas/notifications/{id}/mark-read', 'CienciasBasicasController@markNotificationAsRead')->name('infrastock.ciencias-basicas.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/ciencias-basicas/profile', 'CienciasBasicasController@profile')->name('infrastock.ciencias-basicas.profile');
    Route::put('/infrastock/ciencias-basicas/profile', 'CienciasBasicasController@updateProfile')->name('infrastock.ciencias-basicas.profile.update');
    
    // Reporte de sobrantes
    Route::get('/infrastock/ciencias-basicas/surplus-report', 'CienciasBasicasController@surplusReport')->name('infrastock.ciencias-basicas.surplus-report');
    Route::post('/infrastock/ciencias-basicas/surplus', 'CienciasBasicasController@storeSurplus')->name('infrastock.ciencias-basicas.surplus.store');
    Route::get('/infrastock/ciencias-basicas/surplus/{id}', 'CienciasBasicasController@showSurplus')->name('infrastock.ciencias-basicas.surplus.show');
    Route::delete('/infrastock/ciencias-basicas/surplus/{id}', 'CienciasBasicasController@destroySurplus')->name('infrastock.ciencias-basicas.surplus.destroy');
    
    // Logout de Ciencias Basicas
    Route::post('/infrastock/ciencias-basicas/logout', 'CienciasBasicasController@logout')->name('infrastock.ciencias-basicas.logout');
});

/**
 * Grupo de rutas para Agroindustria con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal de Agroindustria
    Route::get('/infrastock/agroindustria/dashboard', 'AgroindustriaController@dashboard')->name('infrastock.agroindustria.dashboard');
    
    // Visualización de stock en tiempo real
    Route::get('/infrastock/agroindustria/stock', 'AgroindustriaController@stock')->name('infrastock.agroindustria.stock');
    Route::get('/infrastock/agroindustria/equipment/{id}', 'AgroindustriaController@showEquipment')->name('infrastock.agroindustria.equipment.show');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/agroindustria/requests/create', 'AgroindustriaController@createRequest')->name('infrastock.agroindustria.requests.create');
    Route::post('/infrastock/agroindustria/requests', 'AgroindustriaController@storeRequest')->name('infrastock.agroindustria.requests.store');
    Route::get('/infrastock/agroindustria/requests', 'AgroindustriaController@myRequests')->name('infrastock.agroindustria.requests.index');
    Route::get('/infrastock/agroindustria/requests/{id}', 'AgroindustriaController@showRequest')->name('infrastock.agroindustria.requests.show');
    Route::get('/infrastock/agroindustria/requests/{id}/edit', 'AgroindustriaController@editRequest')->name('infrastock.agroindustria.requests.edit');
    Route::put('/infrastock/agroindustria/requests/{id}', 'AgroindustriaController@updateRequest')->name('infrastock.agroindustria.requests.update');
    Route::delete('/infrastock/agroindustria/requests/{id}', 'AgroindustriaController@destroyRequest')->name('infrastock.agroindustria.requests.destroy');
    
    // Notificaciones
    Route::get('/infrastock/agroindustria/notifications', 'AgroindustriaController@notifications')->name('infrastock.agroindustria.notifications');
    Route::post('/infrastock/agroindustria/notifications/{id}/mark-read', 'AgroindustriaController@markNotificationAsRead')->name('infrastock.agroindustria.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/agroindustria/profile', 'AgroindustriaController@profile')->name('infrastock.agroindustria.profile');
    Route::put('/infrastock/agroindustria/profile', 'AgroindustriaController@updateProfile')->name('infrastock.agroindustria.profile.update');
    
    // Reporte de sobrantes
    Route::get('/infrastock/agroindustria/surplus-report', 'AgroindustriaController@surplusReport')->name('infrastock.agroindustria.surplus-report');
    Route::post('/infrastock/agroindustria/surplus', 'AgroindustriaController@storeSurplus')->name('infrastock.agroindustria.surplus.store');
    Route::get('/infrastock/agroindustria/surplus/{id}', 'AgroindustriaController@showSurplus')->name('infrastock.agroindustria.surplus.show');
    Route::delete('/infrastock/agroindustria/surplus/{id}', 'AgroindustriaController@destroySurplus')->name('infrastock.agroindustria.surplus.destroy');
    
    // Logout de Agroindustria
    Route::post('/infrastock/agroindustria/logout', 'AgroindustriaController@logout')->name('infrastock.agroindustria.logout');
});

/**
 * Grupo de rutas para Vigilancia con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal de Vigilancia
    Route::get('/infrastock/vigilancia/dashboard', 'VigilanciaController@dashboard')->name('infrastock.vigilancia.dashboard');
    
    // Visualización de stock en tiempo real
    Route::get('/infrastock/vigilancia/stock', 'VigilanciaController@stock')->name('infrastock.vigilancia.stock');
    Route::get('/infrastock/vigilancia/equipment/{id}', 'VigilanciaController@showEquipment')->name('infrastock.vigilancia.equipment.show');
    
    // Gestión de solicitudes de insumos
    Route::get('/infrastock/vigilancia/requests/create', 'VigilanciaController@createRequest')->name('infrastock.vigilancia.requests.create');
    Route::post('/infrastock/vigilancia/requests', 'VigilanciaController@storeRequest')->name('infrastock.vigilancia.requests.store');
    Route::get('/infrastock/vigilancia/requests', 'VigilanciaController@myRequests')->name('infrastock.vigilancia.requests.index');
    Route::get('/infrastock/vigilancia/requests/{id}', 'VigilanciaController@showRequest')->name('infrastock.vigilancia.requests.show');
    Route::get('/infrastock/vigilancia/requests/{id}/edit', 'VigilanciaController@editRequest')->name('infrastock.vigilancia.requests.edit');
    Route::put('/infrastock/vigilancia/requests/{id}', 'VigilanciaController@updateRequest')->name('infrastock.vigilancia.requests.update');
    Route::delete('/infrastock/vigilancia/requests/{id}', 'VigilanciaController@destroyRequest')->name('infrastock.vigilancia.requests.destroy');
    
    // Notificaciones
    Route::get('/infrastock/vigilancia/notifications', 'VigilanciaController@notifications')->name('infrastock.vigilancia.notifications');
    Route::post('/infrastock/vigilancia/notifications/{id}/mark-read', 'VigilanciaController@markNotificationAsRead')->name('infrastock.vigilancia.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/vigilancia/profile', 'VigilanciaController@profile')->name('infrastock.vigilancia.profile');
    Route::put('/infrastock/vigilancia/profile', 'VigilanciaController@updateProfile')->name('infrastock.vigilancia.profile.update');
    
    // Reporte de sobrantes
    Route::get('/infrastock/vigilancia/surplus-report', 'VigilanciaController@surplusReport')->name('infrastock.vigilancia.surplus-report');
    Route::post('/infrastock/vigilancia/surplus', 'VigilanciaController@storeSurplus')->name('infrastock.vigilancia.surplus.store');
    Route::get('/infrastock/vigilancia/surplus/{id}', 'VigilanciaController@showSurplus')->name('infrastock.vigilancia.surplus.show');
    Route::delete('/infrastock/vigilancia/surplus/{id}', 'VigilanciaController@destroySurplus')->name('infrastock.vigilancia.surplus.destroy');
    
    // Logout de Vigilancia
    Route::post('/infrastock/vigilancia/logout', 'VigilanciaController@logout')->name('infrastock.vigilancia.logout');
});

/**
 * Grupo de rutas para Instructores con middleware de notificaciones.
 * Todas estas rutas requieren que el usuario esté autenticado y comparten notificaciones.
 * Los instructores solo pueden hacer préstamos de herramientas (HERRAMIENTAS).
 */
Route::middleware(['web', 'auth', \Modules\INFRASTOCK\Http\Middleware\ShareNotifications::class])->group(function () {
    // Dashboard principal de Instructores
    Route::get('/infrastock/instructor/dashboard', 'InstructorController@dashboard')->name('infrastock.instructor.dashboard');
    
    // Gestión de préstamos de herramientas
    Route::get('/infrastock/instructor/my-loans', 'InstructorController@myLoans')->name('infrastock.instructor.my-loans');
    Route::post('/infrastock/instructor/store-loan', 'InstructorController@storeLoan')->name('infrastock.instructor.store-loan');
    Route::put('/infrastock/instructor/update-loan/{id}', 'InstructorController@updateLoan')->name('infrastock.instructor.update-loan');
    Route::delete('/infrastock/instructor/delete-loan/{id}', 'InstructorController@destroyLoan')->name('infrastock.instructor.delete-loan');
    Route::post('/infrastock/instructor/return-loan/{id}', 'InstructorController@returnLoan')->name('infrastock.instructor.return-loan');
    
    // Notificaciones
    Route::get('/infrastock/instructor/notifications', 'InstructorController@notifications')->name('infrastock.instructor.notifications');
    Route::post('/infrastock/instructor/notifications/{id}/mark-read', 'InstructorController@markNotificationAsRead')->name('infrastock.instructor.notifications.mark-read');
    
    // Gestión de perfil
    Route::get('/infrastock/instructor/profile', 'InstructorController@profile')->name('infrastock.instructor.profile');
    Route::put('/infrastock/instructor/profile', 'InstructorController@updateProfile')->name('infrastock.instructor.profile.update');
    
    // Logout de Instructores
    Route::post('/infrastock/instructor/logout', 'InstructorController@logout')->name('infrastock.instructor.logout');
});