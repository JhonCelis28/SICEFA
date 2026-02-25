<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\INFRASTOCK\Entities\Notification;
use App\Models\User;
use Modules\SICA\Entities\Person;
use Modules\SICA\Entities\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

/**
 * @class CleaningStaffController
 * @brief Controlador para el dashboard de Personal de Aseo en el módulo INFRASTOCK.
 *
 * Este controlador maneja todas las funcionalidades específicas para el personal de aseo:
 * - Registro de usuarios
 * - Dashboard principal con estadísticas
 * - Solicitud de insumos
 * - Notificaciones de estado de solicitud
 * - Gestión de perfil del usuario
 * - Generación de reportes de sobrantes (solo filtros)
 * - Visualización de historial de insumos
 */
class CleaningStaffController extends Controller
{
    /**
     * Muestra el formulario de registro para personal de aseo.
     * @return Renderable
     */
    public function showRegistrationForm()
    {
        return view('infrastock::cleaning-staff.register');
    }

    /**
     * Procesa el registro de nuevo personal de aseo.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'first_last_name' => 'required|string|max:50',
            'second_last_name' => 'nullable|string|max:50',
            'document_type' => 'required|string|max:10',
            'document_number' => 'required|string|max:20|unique:people,document_number',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_last_name.required' => 'El primer apellido es obligatorio.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'Este número de documento ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Crear la persona
            $person = Person::create([
                'first_name' => $request->first_name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Crear el usuario
            $user = User::create([
                'person_id' => $person->id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nickname' => $request->first_name . ' ' . $request->first_last_name,
            ]);

            // Asignar rol de personal de aseo (si existe)
            $cleaningStaffRole = Role::where('name', 'Personal de Aseo')->first();
            if ($cleaningStaffRole) {
                $user->roles()->attach($cleaningStaffRole->id);
            }

            return redirect()->route('infrastock.cleaning-staff.login')
                ->with('success', 'Registro exitoso. Ya puedes iniciar sesión con tus credenciales.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el usuario. Inténtalo de nuevo.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Muestra el formulario de login para personal de aseo.
     * @return Renderable
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('infrastock.cleaning-staff.dashboard');
        }
        
        return view('infrastock::cleaning-staff.login');
    }

    /**
     * Procesa el login del personal de aseo.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (auth()->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('infrastock.cleaning-staff.dashboard'))
                ->with('success', '¡Bienvenido al sistema INFRASTOCK!');
        }

        return redirect()->back()
            ->withErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'])
            ->withInput($request->except('password'));
    }

    /**
     * Cierra la sesión del usuario.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirigir a la página principal usando URL absoluta para evitar problemas de rutas
        return redirect('/')
            ->with('success', 'Has cerrado sesión correctamente.');
    }

    /**
     * Verifica que el usuario tenga el rol de Personal de Aseo.
     */
    private function verifyRole()
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Aseo', $userRoles) && !in_array('Personal de Aseo', $userRoles)) {
            abort(403, 'No tienes permiso para acceder a esta sección. Solo usuarios con rol de Personal de Aseo pueden acceder.');
        }
    }

    /**
     * Muestra el dashboard principal del personal de aseo.
     * @return Renderable
     */
    public function dashboard()
    {
        $this->verifyRole();
        $user = auth()->user();
        
        // Obtener estadísticas del usuario actual basadas en solicitudes (Request)
        $pendingRequests = \Modules\INFRASTOCK\Entities\Request::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedRequests = \Modules\INFRASTOCK\Entities\Request::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $rejectedRequests = \Modules\INFRASTOCK\Entities\Request::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        // Obtener notificaciones recientes del usuario
        $notifications = \Modules\INFRASTOCK\Entities\Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        $notificationCount = $notifications->where('read_at', null)->count();

        // Obtener el insumo más solicitado (con más solicitudes)
        $mostRequestedSupply = \Modules\INFRASTOCK\Entities\Request::with('items.equipment.category')
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'delivered'])
            ->get()
            ->flatMap(function($request) {
                return $request->items;
            })
            ->groupBy('equipment_id')
            ->map(function($items) {
                return [
                    'equipment' => $items->first()->equipment ?? null,
                    'request_count' => $items->count(),
                    'total_amount_requested' => $items->sum('requested_amount')
                ];
            })
            ->filter(function($item) {
                return $item['equipment'] !== null;
            })
            ->sortByDesc('request_count')
            ->first();

        $mostRequestedSupplyData = $mostRequestedSupply;

        return view('infrastock::cleaning-staff.dashboard', compact(
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'notifications',
            'notificationCount',
            'mostRequestedSupplyData'
        ));
    }

    /**
     * Muestra el formulario para crear una nueva solicitud de insumo.
     * @return Renderable
     */
    public function createRequest()
    {
        $equipments = Equipment::with('category')
            ->orderBy('name')
            ->get();

        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')
            ->get();

        return view('infrastock::cleaning-staff.create-request', compact('equipments', 'productiveUnitWarehouses'));
    }

    /**
     * Almacena una nueva solicitud de insumos (múltiples insumos agrupados).
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
            'equipments' => 'required|array|min:1',
            'equipments.*.amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        // Verificar que se hayan seleccionado insumos
        if (empty($request->equipments)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un insumo para la solicitud.');
        }

        $errors = [];
        $validItems = [];

        // Validar cada insumo seleccionado
        foreach ($request->equipments as $equipmentId => $equipmentData) {
            $equipment = Equipment::find($equipmentId);
            
            if (!$equipment) {
                $errors[] = "El insumo con ID {$equipmentId} no existe.";
                continue;
            }

            $amount = $equipmentData['amount'];

        // Verificar que el insumo tenga cantidad suficiente
            if (!$equipment->hasStockFor($amount)) {
                $errors[] = "No hay suficiente stock disponible para {$equipment->name}. Stock disponible: {$equipment->stock}";
                continue;
            }

            $validItems[] = [
                'equipment_id' => $equipmentId,
                'requested_amount' => $amount,
                'equipment' => $equipment
            ];
        }

        // Si hay errores, mostrar mensaje
        if (!empty($errors)) {
            return redirect()->back()->with('error', 'Errores encontrados: ' . implode(' ', $errors));
        }

        // Si no hay items válidos, mostrar error
        if (empty($validItems)) {
            return redirect()->back()->with('error', 'No se encontraron insumos válidos para la solicitud.');
        }

        try {
            // Crear la solicitud principal
            $newRequest = \Modules\INFRASTOCK\Entities\Request::create([
                'user_id' => auth()->id(),
            'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
            'description' => $request->description,
                'status' => 'pending',
            ]);

            // Crear los items de la solicitud
            foreach ($validItems as $item) {
                \Modules\INFRASTOCK\Entities\RequestItem::create([
                    'request_id' => $newRequest->id,
                    'equipment_id' => $item['equipment_id'],
                    'requested_amount' => $item['requested_amount'],
                    'status' => 'pending',
                ]);
            }

            // Enviar notificación al administrador
            $newRequest->load(['items.equipment', 'user']);
            $this->notifyAdminNewGroupedRequest($newRequest);

            $totalItems = count($validItems);
            $message = $totalItems === 1 
                ? 'Solicitud de insumo creada exitosamente.'
                : "Solicitud creada exitosamente con {$totalItems} insumos.";

            return redirect()->route('infrastock.cleaning-staff.requests.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Muestra todas las solicitudes del usuario actual (agrupadas).
     * @return Renderable
     */
    public function myRequests(Request $request)
    {
        $user = auth()->user();
        
        $query = \Modules\INFRASTOCK\Entities\Request::with([
            'items.equipment.category',
            'productiveUnitWarehouse.productiveUnit',
            'productiveUnitWarehouse.warehouse',
            'user'
        ])->where('user_id', $user->id);
        
        // Filtrar por estado si se proporciona
        $status = $request->get('status');
        if ($status) {
            switch ($status) {
                case 'pending':
                    $query->where('status', 'pending');
                    break;
                case 'approved':
                    $query->where('status', 'approved');
                    break;
                case 'rejected':
                    $query->where('status', 'rejected');
                    break;
            }
        }
        
        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Obtener equipos disponibles para el modal (incluyendo agotados)
        $equipments = Equipment::with('category')
            ->orderBy('name')
            ->get();

        // Obtener unidades productivas disponibles
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')
            ->orderBy('id')
            ->get();

        return view('infrastock::cleaning-staff.my-requests', compact('requests', 'equipments', 'productiveUnitWarehouses'));
    }

    /**
     * Muestra los detalles de una solicitud específica (agrupada).
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRequest($id)
    {
        $request = \Modules\INFRASTOCK\Entities\Request::with([
            'items.equipment.category',
            'productiveUnitWarehouse.productiveUnit',
            'productiveUnitWarehouse.warehouse',
            'user'
        ])->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$request) {
            return response()->json(['error' => 'Solicitud no encontrada'], 404);
        }

        // Preparar los items de la solicitud
        $items = $request->items->map(function($item) {
            return [
                'id' => $item->id,
                'equipment_name' => $item->equipment->name ?? 'N/A',
                'equipment_category' => $item->equipment->category->name ?? 'Sin categoría',
                'requested_amount' => $item->requested_amount,
                'approved_amount' => $item->approved_amount,
                'delivered_amount' => $item->delivered_amount,
                'unit' => $item->equipment->unit ?? 'unidades',
                'status' => $item->status,
                'notes' => $item->notes,
            ];
        });

        return response()->json([
            'id' => $request->id,
            'status' => $request->status,
            'description' => $request->description,
            'productive_unit' => $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A',
            'warehouse' => $request->productiveUnitWarehouse->warehouse->name ?? 'N/A',
            'created_at' => $request->created_at->format('d/m/Y H:i'),
            'approved_at' => $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : null,
            'rejected_at' => $request->rejected_at ? $request->rejected_at->format('d/m/Y H:i') : null,
            'rejection_reason' => $request->rejection_reason,
            'user_name' => $request->user->name ?? 'Personal de Aseo',
            'total_items' => $request->total_items,
            'total_requested_amount' => $request->total_requested_amount,
            'items' => $items
        ]);
    }

    /**
     * Muestra el formulario para editar una solicitud específica (agrupada).
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editRequest($id)
    {
        $request = \Modules\INFRASTOCK\Entities\Request::with([
            'items.equipment.category',
            'productiveUnitWarehouse.productiveUnit',
            'productiveUnitWarehouse.warehouse'
        ])->where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending') // Solo se puede editar si está pendiente
            ->first();

        if (!$request) {
            return response()->json(['error' => 'Solicitud no encontrada o no se puede editar'], 404);
        }

        // Preparar los items de la solicitud para edición
        $items = $request->items->map(function($item) {
            return [
                'id' => $item->id,
                'equipment_id' => $item->equipment_id,
                'equipment_name' => $item->equipment->name ?? 'N/A',
                'equipment_category' => $item->equipment->category->name ?? 'Sin categoría',
                'requested_amount' => $item->requested_amount,
                'unit' => $item->equipment->unit ?? 'unidades',
                'stock' => $item->equipment->stock ?? 0,
            ];
        });

        return response()->json([
            'id' => $request->id,
            'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
            'productive_unit' => $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A',
            'warehouse' => $request->productiveUnitWarehouse->warehouse->name ?? 'N/A',
            'description' => $request->description,
            'created_at' => $request->created_at->format('d/m/Y H:i'),
            'items' => $items
        ]);
    }

    /**
     * Actualiza una solicitud específica (agrupada).
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRequest(Request $request, $id)
    {
        $requestData = \Modules\INFRASTOCK\Entities\Request::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$requestData) {
            return redirect()->route('infrastock.cleaning-staff.requests.index')
                ->with('error', 'Solicitud no encontrada o no se puede editar.');
        }

        $request->validate([
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.requested_amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            // Actualizar la solicitud principal
            $requestData->update([
                'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
                'description' => $request->description,
            ]);

            // Actualizar los items de la solicitud
            foreach ($request->items as $itemId => $itemData) {
                $requestItem = \Modules\INFRASTOCK\Entities\RequestItem::where('id', $itemId)
                    ->where('request_id', $requestData->id)
                    ->first();

                if ($requestItem) {
                    // Verificar stock disponible
                    $equipment = $requestItem->equipment;
                    if (!$equipment->hasStockFor($itemData['requested_amount'])) {
                        return redirect()->route('infrastock.cleaning-staff.requests.index')
                            ->with('error', "No hay suficiente stock disponible para {$equipment->name}. Stock disponible: {$equipment->stock}");
                    }

                    $requestItem->update([
                        'requested_amount' => $itemData['requested_amount'],
                    ]);
                }
            }

            return redirect()->route('infrastock.cleaning-staff.requests.index')
                ->with('success', 'Solicitud actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('infrastock.cleaning-staff.requests.index')
                ->with('error', 'Error al actualizar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una solicitud específica (agrupada).
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyRequest($id)
    {
        $request = \Modules\INFRASTOCK\Entities\Request::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$request) {
            return redirect()->route('infrastock.cleaning-staff.requests.index')
                ->with('error', 'Solicitud no encontrada.');
        }

        // Solo permitir eliminar solicitudes pendientes
        if ($request->status !== 'pending') {
            return redirect()->route('infrastock.cleaning-staff.requests.index')
                ->with('error', 'Solo se pueden eliminar solicitudes pendientes.');
        }

        // Eliminar la solicitud (los items se eliminarán automáticamente por cascade)
        $request->delete();

        return redirect()->route('infrastock.cleaning-staff.requests.index')
            ->with('success', 'Solicitud eliminada exitosamente.');
    }

    /**
     * Enviar notificación al administrador cuando se crea una nueva solicitud agrupada
     */
    private function notifyAdminNewGroupedRequest($request)
    {
        try {
            // Buscar usuarios con roles de administrador de INFRASTOCK por slug o nombre
            $admins = User::whereHas('roles', function($query) {
                $query->where('slug', 'infrastock.admin')
                      ->orWhere('slug', 'superadmin')
                      ->orWhere('name', 'Administrador')
                      ->orWhere('name', 'Super Administrador');
            })->get();

            $totalItems = $request->items->count();
            $equipmentNames = $request->items->pluck('equipment.name')->toArray();
            $equipmentList = implode(', ', array_slice($equipmentNames, 0, 3));
            if (count($equipmentNames) > 3) {
                $equipmentList .= ' y ' . (count($equipmentNames) - 3) . ' más';
            }

            $userName = $request->user->name ?? 'Personal de Aseo';
            $roleName = 'Personal de Aseo';

            foreach ($admins as $admin) {
                // Crear notificación en el dashboard
                Notification::create([
                    'type' => 'request_created',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $admin->id,
                    'data' => [
                        'title' => 'Nueva Solicitud de Insumos',
                        'message' => "El personal de aseo ha creado una nueva solicitud con {$totalItems} insumos: {$equipmentList}.",
                        'request_id' => $request->id,
                        'total_items' => $totalItems,
                        'equipment_list' => $equipmentList,
                        'user_name' => $userName,
                        'action_url' => route('infrastock.admin.supply-requests.index'),
                        'created_at' => now()->format('d/m/Y H:i'),
                    ],
                ]);

                // Enviar correo electrónico al administrador
                if ($admin->email) {
                    try {
                        Mail::to($admin->email)->send(
                            new \Modules\INFRASTOCK\Mail\NewSupplyRequestNotification(
                                $request,
                                $userName,
                                $roleName,
                                $totalItems,
                                $equipmentList
                            )
                        );
                    } catch (\Exception $emailException) {
                        \Log::error('Error enviando correo al administrador ' . $admin->email . ': ' . $emailException->getMessage());
                    }
                }
            }
            
            \Log::info('Notificación y correo enviados a ' . $admins->count() . ' administradores para solicitud #' . $request->id);
        } catch (\Exception $e) {
            // Log del error pero no interrumpir el flujo principal
            \Log::error('Error enviando notificación al administrador: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación al administrador cuando se crea una nueva solicitud (método legacy)
     */
    private function notifyAdminNewRequest($equipment, $amount, $requestId)
    {
        try {
            // Buscar usuarios administradores (asumiendo que tienen un rol específico)
            $admins = User::whereHas('roles', function($query) {
                $query->where('name', 'like', '%admin%')
                      ->orWhere('name', 'like', '%administrador%');
            })->get();

            // Si no hay administradores específicos, usar el primer usuario del sistema
            if ($admins->isEmpty()) {
                $admins = User::take(1)->get();
            }

            foreach ($admins as $admin) {
                Notification::createRequestCreatedNotification(
                    $admin->id,
                    $equipment->name,
                    $amount,
                    $requestId
                );
            }
        } catch (\Exception $e) {
            // Log del error pero no interrumpir el flujo principal
            \Log::error('Error enviando notificación al administrador: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación al personal de aseo cuando se aprueba/rechaza una solicitud
     */
    public function notifyCleaningStaffRequestStatus($userId, $equipmentName, $amount, $requestId, $status)
    {
        try {
            if ($status === 'approved') {
                Notification::createRequestApprovedNotification($userId, $equipmentName, $amount, $requestId);
            } elseif ($status === 'rejected') {
                Notification::createRequestRejectedNotification($userId, $equipmentName, $amount, $requestId);
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación al personal de aseo: ' . $e->getMessage());
        }
    }

    /**
     * Muestra las notificaciones del usuario.
     * @return Renderable
     */
    public function notifications()
    {
        $this->verifyRole();
        $user = auth()->user();
        
        // Obtener notificaciones del usuario usando el modelo Notification de Laravel
        $notifications = \Modules\INFRASTOCK\Entities\Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Cargar las solicitudes relacionadas con las notificaciones
        $requestIds = $notifications->pluck('data')->filter(function($data) {
            return isset($data['request_id']);
        })->pluck('request_id')->unique()->toArray();

        $requests = collect();
        if (!empty($requestIds)) {
            $requests = \Modules\INFRASTOCK\Entities\Request::with([
                'items.equipment.category',
                'productiveUnitWarehouse.productiveUnit',
                'productiveUnitWarehouse.warehouse',
                'user'
            ])->whereIn('id', $requestIds)->get()->keyBy('id');
        }

        return view('infrastock::cleaning-staff.notifications', compact('notifications', 'requests'));
    }

    /**
     * Muestra el formulario de edición del perfil del usuario.
     * @return Renderable
     */
    public function profile()
    {
        $user = auth()->user();
        return view('infrastock::cleaning-staff.profile', compact('user'));
    }

    /**
     * Actualiza el perfil del usuario.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nickname' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        try {
            $data = [
                'name' => $request->name,
            'email' => $request->email,
                'nickname' => $request->nickname,
            ];

            // Solo actualizar la contraseña si se proporciona
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('infrastock.cleaning-staff.profile')
                ->with('success', 'Perfil actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('infrastock.cleaning-staff.profile')
                ->with('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }
    }

    /**
     * Genera reporte de sobrantes (solo filtros, sin exportación).
     * @return Renderable
     */
    public function surplusReport()
    {
        $user = auth()->user();
        
        // Obtener insumos entregados al usuario que podrían tener sobrantes
        $deliveredSupplies = WarehouseMovement::with('equipment')
            ->where('user_id', $user->id)
            ->where('role', 'delivered')
            ->where('item_type', 'equipment')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estadísticas de uso
        $totalDelivered = $deliveredSupplies->sum('amount');
        $uniqueSupplies = $deliveredSupplies->groupBy('movement_id')->count();

        return view('infrastock::cleaning-staff.surplus-report', compact(
            'deliveredSupplies',
            'totalDelivered',
            'uniqueSupplies'
        ));
    }

    /**
     * Muestra el historial de insumos con disponibilidad.
     * @return Renderable
     */
    public function supplyHistory()
    {
        // Obtener todos los insumos con su historial de movimientos
        $supplies = Equipment::with(['category', 'warehouseMovements' => function($query) {
            $query->where('user_id', auth()->id())
                  ->orderBy('created_at', 'desc');
        }])
        ->orderBy('name')
        ->get();

        // Obtener estadísticas de disponibilidad
        $totalSupplies = $supplies->count();
        $availableSupplies = $supplies->filter(function($supply) {
            return $supply->stock > 0;
        })->count();
        $lowStockSupplies = $supplies->filter(function($supply) {
            return $supply->stock <= 5 && $supply->stock > 0;
        })->count();
        $outOfStockSupplies = $supplies->filter(function($supply) {
            return $supply->stock == 0;
        })->count();

        return view('infrastock::cleaning-staff.supply-history', compact(
            'supplies',
            'totalSupplies',
            'availableSupplies',
            'lowStockSupplies',
            'outOfStockSupplies'
        ));
    }
}
