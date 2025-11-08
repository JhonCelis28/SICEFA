<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\INFRASTOCK\Entities\Notification;
use Modules\INFRASTOCK\Entities\Surplus;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Request as InfrastockRequest;
use App\Models\User;
use Modules\SICA\Entities\Person;
use Modules\SICA\Entities\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @class GanaderiaController
 * @brief Controlador para el dashboard de Ganadería en el módulo INFRASTOCK.
 *
 * Este controlador maneja todas las funcionalidades específicas para el Ganadería:
 * - Dashboard principal con estadísticas
 * - Visualización de stock en tiempo real
 * - Solicitud de insumos
 * - Notificaciones de estado de solicitud
 * - Gestión de perfil del usuario
 * - Generación de reportes de sobrantes
 */
class GanaderiaController extends Controller
{
    /**
     * Verifica que el usuario tenga el rol de Ganadería.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function verifyRole()
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Ganadería', $userRoles)) {
            abort(403, 'No tienes permiso para acceder a esta sección. Solo usuarios con rol de Ganadería pueden acceder.');
        }
    }

    /**
     * Muestra el dashboard principal del Ganadería.
     * @return Renderable
     */
    public function dashboard()
    {
        $this->verifyRole();
        $user = auth()->user();
        
        // Obtener estadísticas del usuario actual basadas en solicitudes
        $pendingRequests = InfrastockRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedRequests = InfrastockRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $deliveredRequests = InfrastockRequest::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->count();

        $rejectedRequests = InfrastockRequest::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        // Obtener notificaciones recientes del usuario
        $notifications = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        $notificationCount = $notifications->count();

        // Obtener el insumo más solicitado (con más solicitudes)
        $mostRequestedSupply = InfrastockRequest::with('items.equipment.category')
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

        return view('infrastock::ganaderia.dashboard', compact(
            'pendingRequests',
            'approvedRequests', 
            'deliveredRequests',
            'rejectedRequests',
            'notifications',
            'notificationCount',
            'mostRequestedSupplyData'
        ));
    }

    /**
     * Muestra el stock disponible en tiempo real con filtros.
     * @param Request $request
     * @return Renderable
     */
    public function stock(Request $request)
    {
        $this->verifyRole();
        $query = Equipment::with('category')
            ->orderBy('name');

        // Filtrar: excluir categorías de aseo para Ganadería (herramientas e insumos generales)
        $query = $this->excludeCleaningCategories($query);

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por búsqueda (nombre o código)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filtro por stock bajo
        if ($request->has('low_stock')) {
            $equipments = $query->get();
            $equipments = $equipments->filter(function($equipment) {
                return $equipment->stock <= 10 && $equipment->stock > 0;
            });
        } else {
            $equipments = $query->get();
        }

        // Obtener solo categorías permitidas (excluir aseo)
        $categories = InfrastockCategory::where(function($q) {
                $q->where('name', 'not like', '%aseo%')
                  ->where('name', 'not like', '%Aseo%')
                  ->where('name', 'not like', '%limpieza%')
                  ->where('name', 'not like', '%Limpieza%')
                  ->where('name', 'not like', '%cleaning%')
                  ->where('name', 'not like', '%Cleaning%');
            })
            ->orderBy('name')
            ->get();

        // Estadísticas generales
        $totalEquipment = Equipment::count();
        $totalStock = Equipment::get()->sum('stock');
        $lowStockCount = Equipment::get()->filter(function($equipment) {
            return $equipment->stock <= 10 && $equipment->stock > 0;
        })->count();
        $outOfStockCount = Equipment::get()->filter(function($equipment) {
            return $equipment->stock == 0;
        })->count();

        return view('infrastock::ganaderia.stock', compact(
            'equipments',
            'categories',
            'totalEquipment',
            'totalStock',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /**
     * Muestra los detalles de un insumo específico.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showEquipment($id)
    {
        $this->verifyRole();
        $equipment = Equipment::with('category')->find($id);

        if (!$equipment) {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }

        return response()->json([
            'id' => $equipment->id,
            'name' => $equipment->name,
            'category' => $equipment->category->name ?? 'Sin categoría',
            'stock' => $equipment->stock,
            'amount' => $equipment->amount,
            'initial_amount' => $equipment->initial_amount ?? $equipment->amount,
            'unit' => $equipment->unit ?? 'unidades',
            'price' => $equipment->price,
            'updated_at' => $equipment->updated_at->format('d/m/Y H:i'),
            'low_stock' => $equipment->stock <= 10 && $equipment->stock > 0,
            'out_of_stock' => $equipment->stock == 0,
        ]);
    }

    /**
     * Helper: Excluye categorías de aseo para el Ganadería.
     * El Ganadería puede solicitar herramientas e insumos generales, NO de aseo.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function excludeCleaningCategories($query)
    {
        return $query->where(function($q) {
            // Opción 1: Tiene categoría que NO es de aseo
            $q->whereHas('category', function($categoryQuery) {
                $categoryQuery->where(function($excludeQuery) {
                    // Excluir aseo/limpieza explícitamente
                    $excludeQuery->where('name', 'not like', '%aseo%')
                                 ->where('name', 'not like', '%Aseo%')
                                 ->where('name', 'not like', '%limpieza%')
                                 ->where('name', 'not like', '%Limpieza%')
                                 ->where('name', 'not like', '%cleaning%')
                                 ->where('name', 'not like', '%Cleaning%');
                });
            })
            // Opción 2: No tiene categoría (permitir para flexibilidad)
            ->orWhereNull('category_id');
        });
    }

    /**
     * Retorna los datos necesarios para el formulario de solicitud (para modal AJAX).
     * Si es una petición AJAX retorna JSON, sino redirige a la página de solicitudes.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createRequest(Request $request)
    {
        $this->verifyRole();
        // Filtrar equipos: excluir categorías de aseo (herramientas e insumos generales)
        $query = Equipment::with('category')
            ->orderBy('name');
        
        $equipments = $this->excludeCleaningCategories($query)->get()
            ->map(function($equipment) {
                return [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'category' => $equipment->category->name ?? 'Sin categoría',
                    'stock' => $equipment->stock,
                    'unit' => $equipment->unit ?? 'unidades',
                    'description' => $equipment->description ?? '',
                    'price' => $equipment->price ?? 0,
                ];
            });

        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')
            ->get()
            ->map(function($puw) {
                return [
                    'id' => $puw->id,
                    'productive_unit' => $puw->productiveUnit->name ?? 'Sin nombre',
                    'warehouse' => $puw->warehouse->name ?? 'Sin nombre',
                    'full_name' => ($puw->productiveUnit->name ?? 'Sin nombre') . ' - ' . ($puw->warehouse->name ?? 'Sin nombre'),
                ];
            });

        // Si es una petición AJAX o espera JSON, retornar JSON
        $acceptHeader = $request->header('Accept', '');
        if ($request->ajax() || 
            $request->wantsJson() || 
            $request->expectsJson() ||
            $acceptHeader === 'application/json' ||
            strpos($acceptHeader, 'application/json') !== false) {
            return response()->json([
                'success' => true,
                'equipments' => $equipments,
                'productiveUnitWarehouses' => $productiveUnitWarehouses,
            ]);
        }

        // Si no es AJAX, redirigir a la página de solicitudes donde está el modal
        return redirect()->route('infrastock.ganaderia.requests.index');
    }

    /**
     * Almacena una nueva solicitud de insumos (múltiples insumos agrupados).
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRequest(Request $request)
    {
        $this->verifyRole();
        $request->validate([
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
            'equipments' => 'required|array|min:1',
            'equipments.*.amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ], [
            'productive_unit_warehouse_id.required' => 'Debe seleccionar una unidad productiva.',
            'equipments.required' => 'Debe seleccionar al menos un insumo.',
            'equipments.min' => 'Debe seleccionar al menos un insumo.',
            'equipments.*.amount.required' => 'La cantidad es obligatoria.',
            'equipments.*.amount.min' => 'La cantidad debe ser mayor a 0.',
        ]);

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

        if (!empty($errors)) {
            $errorMessage = 'Errores encontrados: ' . implode(' ', $errors);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 422);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        if (empty($validItems)) {
            $errorMessage = 'No se encontraron insumos válidos para la solicitud.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 422);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        try {
            // Crear la solicitud principal
            $newRequest = InfrastockRequest::create([
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
            $this->notifyAdminNewRequest($newRequest);

            // Crear notificación para el Ganadería
            Notification::create([
                'type' => 'request_created',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => auth()->id(),
                'data' => [
                    'title' => 'Solicitud Registrada',
                    'message' => "Tu solicitud #{$newRequest->id} ha sido registrada y está en proceso de revisión.",
                    'request_id' => $newRequest->id,
                    'created_at' => now()->format('d/m/Y H:i'),
                ],
            ]);

            $totalItems = count($validItems);
            $message = $totalItems === 1 
                ? 'Solicitud de insumo creada exitosamente.'
                : "Solicitud creada exitosamente con {$totalItems} insumos.";

            // Si es una petición AJAX, retornar JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'request_id' => $newRequest->id
                ]);
            }

            return redirect()->route('infrastock.ganaderia.requests.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            $errorMessage = 'Error al crear la solicitud: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 500);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Muestra todas las solicitudes del usuario actual.
     * @param Request $request
     * @return Renderable
     */
    public function myRequests(Request $request)
    {
        $this->verifyRole();
        $user = auth()->user();
        
        $query = InfrastockRequest::with([
            'items.equipment.category',
            'productiveUnitWarehouse.productiveUnit',
            'productiveUnitWarehouse.warehouse',
            'user'
        ])->where('user_id', $user->id);
        
        // Filtrar por estado si se proporciona
        $status = $request->get('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Búsqueda por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('items.equipment', function($eq) use ($search) {
                      $eq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Obtener equipos disponibles para el modal
        $equipments = Equipment::with('category')
            ->orderBy('name')
            ->get();

        // Obtener unidades productivas disponibles
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')
            ->orderBy('id')
            ->get();

        return view('infrastock::ganaderia.my-requests', compact('requests', 'equipments', 'productiveUnitWarehouses'));
    }

    /**
     * Muestra los detalles de una solicitud específica.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRequest($id)
    {
        $this->verifyRole();
        $request = InfrastockRequest::with([
            'items.equipment.category',
            'productiveUnitWarehouse.productiveUnit',
            'productiveUnitWarehouse.warehouse',
            'user'
        ])->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$request) {
            abort(404, 'Solicitud no encontrada');
        }

        return view('infrastock::ganaderia.show-request', compact('request'));
    }

    /**
     * Muestra las notificaciones del usuario.
     * @return Renderable
     */
    public function notifications()
    {
        $this->verifyRole();
        $user = auth()->user();
        
        $notifications = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Cargar las solicitudes relacionadas con las notificaciones
        $requestIds = $notifications->pluck('data')->filter(function($data) {
            return isset($data['request_id']);
        })->pluck('request_id')->unique()->toArray();

        $requests = collect();
        if (!empty($requestIds)) {
            $requests = InfrastockRequest::with([
                'items.equipment.category',
                'productiveUnitWarehouse.productiveUnit',
                'productiveUnitWarehouse.warehouse',
                'user'
            ])->whereIn('id', $requestIds)->get()->keyBy('id');
        }

        return view('infrastock::ganaderia.notifications', compact('notifications', 'requests'));
    }

    /**
     * Marca una notificación como leída.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markNotificationAsRead($id)
    {
        $this->verifyRole();
        $notification = Notification::where('id', $id)
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        if (isset($notification->data['read_at'])) {
            return response()->json(['message' => 'Notificación ya estaba marcada como leída']);
        }

        $data = $notification->data;
        $data['read_at'] = now()->format('d/m/Y H:i');
        $notification->data = $data;
        $notification->save();

        return response()->json(['success' => true, 'message' => 'Notificación marcada como leída']);
    }

    /**
     * Muestra el formulario de edición del perfil del usuario.
     * @return Renderable
     */
    public function profile()
    {
        $this->verifyRole();
        $user = auth()->user();
        return view('infrastock::ganaderia.profile', compact('user'));
    }

    /**
     * Actualiza el perfil del usuario.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $this->verifyRole();
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nickname' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        try {
            $data = [
                'email' => $request->email,
                'nickname' => $request->nickname,
            ];

            // Actualizar datos de la persona
            if ($user->person) {
                $personData = [];
                if ($request->has('phone')) {
                    $personData['phone'] = $request->phone;
                }
                if ($request->has('address')) {
                    $personData['address'] = $request->address;
                }
                if (!empty($personData)) {
                    $user->person->update($personData);
                }
            }

            // Solo actualizar la contraseña si se proporciona
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el módulo de reportes de sobrantes.
     * @param Request $request
     * @return Renderable
     */
    public function surplusReport(Request $request)
    {
        $this->verifyRole();
        $user = auth()->user();

        // Obtener solicitudes entregadas del usuario para seleccionar en el formulario
        $deliveredRequests = InfrastockRequest::with('items.equipment.category')
            ->where('user_id', $user->id)
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener herramientas e insumos generales (excluir aseo)
        $query = Equipment::with('category')
            ->orderBy('name');
        
        $equipments = $this->excludeCleaningCategories($query)->get();

        // Obtener reportes de sobrantes del usuario
        $query = Surplus::with('equipment.category')
            ->where('user_id', $user->id);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('equipment', function($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        // Filtro por fecha
        if ($request->filled('start_date')) {
            $query->where('surplus_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('surplus_date', '<=', $request->end_date);
        }

        $surpluses = $query->orderBy('surplus_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('infrastock::ganaderia.surplus-report', compact('surpluses', 'deliveredRequests', 'equipments'));
    }

    /**
     * Almacena un nuevo reporte de sobrantes.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSurplus(Request $request)
    {
        $this->verifyRole();
        $request->validate([
            'request_id' => 'nullable|exists:requests,id',
            'equipment_id' => 'required|exists:equipments,id',
            'surplus_amount' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
            'surplus_date' => 'required|date|before_or_equal:today',
        ], [
            'equipment_id.required' => 'Debe seleccionar un insumo.',
            'surplus_amount.required' => 'Debe especificar la cantidad que sobró.',
            'surplus_amount.min' => 'La cantidad debe ser mayor a 0.',
            'reason.required' => 'Debe especificar la causa del sobrante.',
            'surplus_date.required' => 'Debe especificar la fecha del sobrante.',
            'surplus_date.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        // Verificar que el equipo no sea de categoría de aseo
        $equipment = Equipment::with('category')->find($request->equipment_id);
        
        if (!$equipment) {
            return redirect()->back()->with('error', 'El insumo seleccionado no existe.');
        }

        // Verificar que el equipo no sea de aseo
        if ($equipment->category) {
            $categoryName = strtolower($equipment->category->name);
            if (strpos($categoryName, 'aseo') !== false || 
                strpos($categoryName, 'limpieza') !== false || 
                strpos($categoryName, 'cleaning') !== false) {
                return redirect()->back()->with('error', 'No puedes registrar sobrantes de insumos de aseo. Solo herramientas e insumos generales.');
            }
        }

        // Si se proporcionó una solicitud, validarla
        if ($request->filled('request_id')) {
            $infrastockRequest = InfrastockRequest::where('id', $request->request_id)
                ->where('user_id', auth()->id())
                ->where('status', 'delivered')
                ->first();

            if (!$infrastockRequest) {
                return redirect()->back()->with('error', 'La solicitud seleccionada no es válida o no ha sido entregada.');
            }

            // Verificar que el insumo esté en la solicitud
            $requestItem = \Modules\INFRASTOCK\Entities\RequestItem::where('request_id', $infrastockRequest->id)
                ->where('equipment_id', $request->equipment_id)
                ->first();

            if (!$requestItem) {
                return redirect()->back()->with('error', 'El insumo seleccionado no pertenece a la solicitud.');
            }

            // Validar que la cantidad sobrante no supere la cantidad entregada
            $deliveredAmount = $requestItem->delivered_amount ?? $requestItem->approved_amount ?? $requestItem->requested_amount;
            if ($request->surplus_amount > $deliveredAmount) {
                return redirect()->back()->with('error', "La cantidad ingresada ({$request->surplus_amount}) supera la cantidad entregada ({$deliveredAmount}) en la solicitud.");
            }
        }

        try {
            $surplus = Surplus::create([
                'equipment_id' => $request->equipment_id,
                'user_id' => auth()->id(),
                'request_id' => $request->request_id ?? null,
                'surplus_amount' => $request->surplus_amount,
                'reason' => $request->reason,
                'surplus_date' => $request->surplus_date,
            ]);

            // Notificar al administrador si existe el método
            if (method_exists($this, 'notifyAdminSurplus')) {
                $this->notifyAdminSurplus($surplus);
            }

            return redirect()->route('infrastock.ganaderia.surplus-report')
                ->with('success', 'Reporte de sobrantes registrado exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al registrar sobrante: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un sobrante específico.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showSurplus($id)
    {
        $this->verifyRole();
        $surplus = Surplus::with('equipment.category')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$surplus) {
            return response()->json(['error' => 'Sobrante no encontrado'], 404);
        }

        return response()->json([
            'id' => $surplus->id,
            'equipment_name' => $surplus->equipment->name,
            'equipment_category' => $surplus->equipment->category->name ?? 'Sin categoría',
            'surplus_amount' => $surplus->surplus_amount,
            'unit' => $surplus->equipment->unit ?? 'unidades',
            'reason' => $surplus->reason,
            'surplus_date' => $surplus->surplus_date->format('d/m/Y'),
            'created_at' => $surplus->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Elimina un registro de sobrante.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroySurplus($id)
    {
        $this->verifyRole();
        $surplus = Surplus::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$surplus) {
            return redirect()->route('infrastock.ganaderia.surplus-report')
                ->with('error', 'Sobrante no encontrado.');
        }

        try {
            $surplus->delete();
            return redirect()->route('infrastock.ganaderia.surplus-report')
                ->with('success', 'Sobrante eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('infrastock.ganaderia.surplus-report')
                ->with('error', 'Error al eliminar el sobrante: ' . $e->getMessage());
        }
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
        
        return redirect()->route('cefa.welcome')
            ->with('success', 'Has cerrado sesión correctamente.');
    }

    /**
     * Enviar notificación al administrador cuando se crea una nueva solicitud
     */
    private function notifyAdminNewRequest($request)
    {
        try {
            // Buscar usuarios con roles de administrador
            $adminRoleIds = [1, 5, 7, 16, 19, 24, 30, 38];
            $admins = User::whereHas('roles', function($query) use ($adminRoleIds) {
                $query->whereIn('roles.id', $adminRoleIds);
            })->get();

            if ($admins->isEmpty()) {
                $admins = User::whereHas('roles', function($query) {
                    $query->where('name', 'Administrador')
                          ->orWhere('name', 'Super Administrador');
                })->get();
            }

            if ($admins->isEmpty()) {
                $admins = User::take(1)->get();
            }

            $totalItems = $request->items->count();
            $equipmentNames = $request->items->pluck('equipment.name')->toArray();
            $equipmentList = implode(', ', array_slice($equipmentNames, 0, 3));
            if (count($equipmentNames) > 3) {
                $equipmentList .= ' y ' . (count($equipmentNames) - 3) . ' más';
            }

            foreach ($admins as $admin) {
                Notification::create([
                    'type' => 'request_created',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $admin->id,
                    'data' => [
                        'title' => 'Nueva Solicitud de Insumos',
                        'message' => "El Ganadería ha creado una nueva solicitud con {$totalItems} insumos: {$equipmentList}.",
                        'request_id' => $request->id,
                        'total_items' => $totalItems,
                        'equipment_list' => $equipmentList,
                        'user_name' => $request->user->nickname ?? $request->user->name ?? 'Ganadería',
                        'action_url' => route('infrastock.admin.requests.index'),
                        'created_at' => now()->format('d/m/Y H:i'),
                    ],
                ]);
            }
            
            \Log::info('Notificación enviada a ' . $admins->count() . ' administradores para solicitud #' . $request->id);
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación al administrador: ' . $e->getMessage());
        }
    }

    /**
     * Notificar al administrador sobre un nuevo reporte de sobrantes
     */
    private function notifyAdminSurplus($surplus)
    {
        try {
            $adminRoleIds = [1, 5, 7, 16, 19, 24, 30, 38];
            $admins = User::whereHas('roles', function($query) use ($adminRoleIds) {
                $query->whereIn('roles.id', $adminRoleIds);
            })->get();

            if ($admins->isEmpty()) {
                $admins = User::whereHas('roles', function($query) {
                    $query->where('name', 'Administrador')
                          ->orWhere('name', 'Super Administrador');
                })->get();
            }

            foreach ($admins as $admin) {
                Notification::create([
                    'type' => 'surplus_reported',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $admin->id,
                    'data' => [
                        'title' => 'Nuevo Reporte de Sobrantes',
                        'message' => "El Ganadería ha generado un reporte de sobrantes para {$surplus->equipment->name}.",
                        'surplus_id' => $surplus->id,
                        'equipment_name' => $surplus->equipment->name,
                        'surplus_amount' => $surplus->surplus_amount,
                        'user_name' => auth()->user()->nickname ?? auth()->user()->name ?? 'Ganadería',
                        'action_url' => route('infrastock.admin.requests.index'),
                        'created_at' => now()->format('d/m/Y H:i'),
                    ],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de sobrantes al administrador: ' . $e->getMessage());
        }
    }
}

