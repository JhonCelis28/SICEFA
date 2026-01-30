<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\INFRASTOCK\Entities\Notification;
use App\Models\User;
use Carbon\Carbon;

/**
 * @class InstructorController
 * @brief Controlador para el dashboard de Instructores en el módulo INFRASTOCK.
 *
 * Este controlador maneja todas las funcionalidades específicas para Instructores:
 * - Dashboard principal con estadísticas de préstamos de herramientas
 * - Visualización y gestión de préstamos de herramientas
 * - Creación de nuevos préstamos de herramientas
 */
class InstructorController extends Controller
{
    /**
     * Verifica que el usuario tenga el rol de Instructor.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function verifyRole()
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Instructor', $userRoles)) {
            abort(403, 'No tienes permiso para acceder a esta sección. Solo usuarios con rol de Instructor pueden acceder.');
        }
    }

    /**
     * Muestra el dashboard principal de Instructores.
     * @return Renderable
     */
    public function dashboard()
    {
        $this->verifyRole();
        $user = auth()->user();
        
        // Obtener estadísticas del usuario actual basadas en préstamos de herramientas
        // Un préstamo está activo si no tiene una devolución correspondiente
        $allLoans = WarehouseMovement::where('user_id', $user->id)
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->get();
        
        $activeLoans = $allLoans->filter(function($loan) {
            // Verificar si existe una devolución para este préstamo
            $hasReturn = WarehouseMovement::where('user_id', $loan->user_id)
                ->where('item_type', 'tool')
                ->where('role', 'Devolución')
                ->where('movement_id', $loan->movement_id ?? $loan->id)
                ->exists();
            return !$hasReturn;
        })->count();

        $totalLoans = WarehouseMovement::where('user_id', $user->id)
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->count();

        $returnedLoans = WarehouseMovement::where('user_id', $user->id)
            ->where('item_type', 'tool')
            ->where('role', 'Devolución')
            ->count();

        // Obtener herramientas más prestadas por este instructor
        $mostLoanedTools = WarehouseMovement::where('user_id', $user->id)
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->with('tool')
            ->get()
            ->groupBy('tool_id')
            ->map(function($loans) {
                $tool = $loans->first()->tool;
                return [
                    'tool' => $tool,
                    'loan_count' => $loans->count()
                ];
            })
            ->filter(function($item) {
                return $item['tool'] !== null;
            })
            ->sortByDesc('loan_count')
            ->take(1)
            ->first();

        // Obtener notificaciones recientes del usuario
        $notifications = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        $notificationCount = $notifications->where('read_at', null)->count();

        // Obtener préstamos recientes
        $recentLoans = WarehouseMovement::where('user_id', $user->id)
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->with('tool')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('infrastock::instructor.dashboard', compact(
            'activeLoans',
            'totalLoans',
            'returnedLoans',
            'mostLoanedTools',
            'notifications',
            'notificationCount',
            'recentLoans'
        ));
    }

    /**
     * Muestra todos los préstamos de herramientas del instructor.
     * @param Request $request
     * @return Renderable
     */
    public function myLoans(Request $request)
    {
        $this->verifyRole();
        $user = auth()->user();
        
        $query = WarehouseMovement::with(['tool', 'productiveUnitWarehouse.productiveUnit', 'productiveUnitWarehouse.warehouse'])
            ->where('user_id', $user->id)
            ->where('item_type', 'tool');
        
        // Filtrar por tipo de movimiento
        $role = $request->get('role');
        if ($role) {
            $query->where('role', $role);
        }

        // Búsqueda por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('tool', function($toolQuery) use ($search) {
                      $toolQuery->where('nombre', 'like', "%{$search}%")
                                ->orWhere('placa', 'like', "%{$search}%");
                  });
            });
        }
        
        $loans = $query->orderBy('created_at', 'desc')->paginate(10);

        // Obtener herramientas disponibles para préstamo
        $tools = Tool::orderBy('nombre')->get();

        // Obtener unidades productivas disponibles
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')
            ->orderBy('id')
            ->get();

        return view('infrastock::instructor.my-loans', compact('loans', 'tools', 'productiveUnitWarehouses'));
    }

    /**
     * Almacena un nuevo préstamo de herramienta.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLoan(Request $request)
    {
        $this->verifyRole();
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
            'purpose' => 'required|string|max:1000',
            'required_date' => 'required|date|after_or_equal:today',
            'amount' => 'nullable|integer|min:1',
            'delivery_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'tool_id.required' => 'Debe seleccionar una herramienta.',
            'productive_unit_warehouse_id.required' => 'Debe seleccionar una unidad productiva.',
            'purpose.required' => 'La finalidad del préstamo es obligatoria.',
            'purpose.max' => 'La finalidad no puede exceder 1000 caracteres.',
            'required_date.required' => 'La fecha requerida es obligatoria.',
            'required_date.date' => 'La fecha requerida debe ser una fecha válida.',
            'required_date.after_or_equal' => 'La fecha requerida debe ser hoy o una fecha futura.',
            'amount.integer' => 'La cantidad debe ser un número entero.',
            'amount.min' => 'La cantidad debe ser al menos 1.',
            'delivery_image.image' => 'El archivo debe ser una imagen.',
            'delivery_image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'delivery_image.max' => 'La imagen no puede pesar más de 2MB.',
        ]);

        $tool = Tool::find($request->tool_id);
        
        if (!$tool) {
            return redirect()->back()->with('error', 'La herramienta seleccionada no existe.');
        }

        $productiveUnitWarehouse = ProductiveUnitWarehouse::find($request->productive_unit_warehouse_id);
        if (!$productiveUnitWarehouse) {
            return redirect()->back()->with('error', 'La unidad productiva seleccionada no existe.');
        }

        try {
            // Preparar datos para el préstamo
            $data = [
                'productive_unit_warehouse_id' => $productiveUnitWarehouse->id,
                'movement_id' => $tool->id,
                'item_type' => 'tool',
                'user_id' => auth()->id(),
                'role' => 'Préstamo',
                'status' => 'pending', // Estado pendiente hasta que el administrador lo apruebe
                'purpose' => $request->purpose,
                'required_date' => $request->required_date,
            ];
            
            // Agregar cantidad si se proporciona
            if ($request->filled('amount')) {
                $data['amount'] = $request->amount;
            }
            
            // Manejar la carga de imagen de entrega
            if ($request->hasFile('delivery_image')) {
                $deliveryImage = $request->file('delivery_image');
                $deliveryImagePath = $deliveryImage->store('loan-deliveries', 'public');
                $data['delivery_image'] = $deliveryImagePath;
            }
            
            // Crear el movimiento de préstamo en WarehouseMovement
            // Esto se guardará en el módulo "Préstamos y Devoluciones" del administrador
            $warehouseMovement = WarehouseMovement::create($data);

            // Cargar relaciones necesarias para las notificaciones
            $warehouseMovement->load(['tool', 'user', 'productiveUnitWarehouse']);

            // Enviar notificación al administrador sobre el nuevo préstamo
            $this->notifyAdminNewLoan($warehouseMovement);

            // Crear notificación para el instructor confirmando que registró el préstamo
            try {
                $notification = new Notification();
                $notification->type = 'loan_created';
                $notification->notifiable_type = 'App\Models\User';
                $notification->notifiable_id = auth()->id();
                $notification->data = [
                    'title' => 'Préstamo Registrado',
                    'message' => "Has registrado el préstamo de la herramienta: {$tool->nombre}.",
                    'tool_id' => $tool->id,
                    'loan_id' => $warehouseMovement->id,
                    'created_at' => now()->format('d/m/Y H:i'),
                ];
                $notification->save();
            } catch (\Exception $notificationError) {
                // Si falla la notificación, no afecta el préstamo
                \Log::warning('No se pudo crear la notificación del préstamo: ' . $notificationError->getMessage());
            }

            return redirect()->route('infrastock.instructor.my-loans')
                ->with('success', 'Préstamo de herramienta registrado exitosamente. El préstamo ha sido registrado en el módulo de Préstamos y Devoluciones.');

        } catch (\Exception $e) {
            \Log::error('Error al registrar préstamo: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un préstamo de herramienta existente.
     * Solo se puede editar si el préstamo está pendiente.
     * @param Request $request
     * @param int $id ID del préstamo (WarehouseMovement)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLoan(Request $request, $id)
    {
        $this->verifyRole();
        $user = auth()->user();
        
        // Buscar el préstamo y verificar que pertenece al usuario y está pendiente
        $loan = WarehouseMovement::where('id', $id)
            ->where('user_id', $user->id)
            ->where('role', 'Préstamo')
            ->where('item_type', 'tool')
            ->where('status', 'pending')
            ->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Préstamo no encontrado o no se puede editar (solo se pueden editar préstamos pendientes).');
        }

        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
            'purpose' => 'required|string|max:1000',
            'required_date' => 'required|date|after_or_equal:today',
            'amount' => 'nullable|integer|min:1',
            'delivery_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'tool_id.required' => 'Debe seleccionar una herramienta.',
            'productive_unit_warehouse_id.required' => 'Debe seleccionar una unidad productiva.',
            'purpose.required' => 'La finalidad del préstamo es obligatoria.',
            'purpose.max' => 'La finalidad no puede exceder 1000 caracteres.',
            'required_date.required' => 'La fecha requerida es obligatoria.',
            'required_date.date' => 'La fecha requerida debe ser una fecha válida.',
            'required_date.after_or_equal' => 'La fecha requerida debe ser hoy o una fecha futura.',
            'amount.integer' => 'La cantidad debe ser un número entero.',
            'amount.min' => 'La cantidad debe ser al menos 1.',
            'delivery_image.image' => 'El archivo debe ser una imagen.',
            'delivery_image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'delivery_image.max' => 'La imagen no puede pesar más de 2MB.',
        ]);

        try {
            // Preparar datos para actualizar
            $data = [
                'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
                'movement_id' => $request->tool_id,
                'purpose' => $request->purpose,
                'required_date' => $request->required_date,
            ];
            
            // Agregar cantidad si se proporciona
            if ($request->filled('amount')) {
                $data['amount'] = $request->amount;
            } else {
                $data['amount'] = null;
            }
            
            // Manejar la carga de nueva imagen de entrega (solo si se sube una nueva)
            if ($request->hasFile('delivery_image')) {
                // Eliminar imagen anterior si existe
                if ($loan->delivery_image && \Storage::disk('public')->exists($loan->delivery_image)) {
                    \Storage::disk('public')->delete($loan->delivery_image);
                }
                
                $deliveryImage = $request->file('delivery_image');
                $deliveryImagePath = $deliveryImage->store('loan-deliveries', 'public');
                $data['delivery_image'] = $deliveryImagePath;
            }
            
            // Actualizar el préstamo
            $loan->update($data);

            return redirect()->route('infrastock.instructor.my-loans')
                ->with('success', 'Préstamo actualizado exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar préstamo: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un préstamo de herramienta.
     * Solo se puede eliminar si el préstamo está pendiente.
     * @param int $id ID del préstamo (WarehouseMovement)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyLoan($id)
    {
        $this->verifyRole();
        $user = auth()->user();
        
        // Buscar el préstamo y verificar que pertenece al usuario y está pendiente
        $loan = WarehouseMovement::where('id', $id)
            ->where('user_id', $user->id)
            ->where('role', 'Préstamo')
            ->where('item_type', 'tool')
            ->where('status', 'pending')
            ->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Préstamo no encontrado o no se puede eliminar (solo se pueden eliminar préstamos pendientes).');
        }

        try {
            // Eliminar imagen de entrega si existe
            if ($loan->delivery_image && \Storage::disk('public')->exists($loan->delivery_image)) {
                \Storage::disk('public')->delete($loan->delivery_image);
            }
            
            // Eliminar el préstamo
            $loan->delete();

            return redirect()->route('infrastock.instructor.my-loans')
                ->with('success', 'Préstamo eliminado exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al eliminar préstamo: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Registra la devolución de una herramienta prestada.
     * @param Request $request
     * @param int $id ID del préstamo (WarehouseMovement)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function returnLoan(Request $request, $id)
    {
        $this->verifyRole();
        
        $request->validate([
            'description' => 'required|string|max:1000',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'return_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'description.required' => 'La descripción de entrega es obligatoria.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'imagen.max' => 'La imagen no puede pesar más de 2MB.',
            'return_image.image' => 'El archivo debe ser una imagen.',
            'return_image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'return_image.max' => 'La imagen no puede pesar más de 2MB.',
        ]);

        $loan = WarehouseMovement::with('tool')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->where('role', 'Préstamo')
            ->where('item_type', 'tool')
            ->where('status', 'approved')
            ->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Préstamo no encontrado o no está aprobado.');
        }

        // Verificar si ya existe una devolución para este préstamo
        $existingReturn = WarehouseMovement::where('user_id', $loan->user_id)
            ->where('movement_id', $loan->movement_id)
            ->where('item_type', 'tool')
            ->where('role', 'Devolución')
            ->first();

        if ($existingReturn) {
            return redirect()->back()->with('error', 'Esta herramienta ya fue devuelta anteriormente.');
        }

        try {
            $data = [
                'productive_unit_warehouse_id' => $loan->productive_unit_warehouse_id,
                'movement_id' => $loan->movement_id,
                'item_type' => 'tool',
                'user_id' => auth()->id(),
                'role' => 'Devolución',
                'status' => 'pending', // Pendiente de aprobación del administrador
                'description' => $request->description,
            ];
            
            // Manejar la carga de imagen (legacy, mantener compatibilidad)
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $imagenPath = $imagen->store('loan-returns', 'public');
                $data['imagen'] = $imagenPath;
            }
            
            // Manejar la carga de imagen de devolución
            if ($request->hasFile('return_image')) {
                $returnImage = $request->file('return_image');
                $returnImagePath = $returnImage->store('loan-returns', 'public');
                $data['return_image'] = $returnImagePath;
            }
            
            // Crear el movimiento de devolución
            WarehouseMovement::create($data);

            return redirect()->route('infrastock.instructor.my-loans')
                ->with('success', 'Devolución registrada exitosamente. Está pendiente de aprobación por el administrador.');

        } catch (\Exception $e) {
            \Log::error('Error al registrar devolución: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Muestra las notificaciones del instructor.
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

        return view('infrastock::instructor.notifications', compact('notifications'));
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
     * Muestra el formulario de edición del perfil del instructor.
     * @return Renderable
     */
    public function profile()
    {
        $this->verifyRole();
        $user = auth()->user();
        return view('infrastock::instructor.profile', compact('user'));
    }

    /**
     * Actualiza el perfil del instructor.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

            // Solo actualizar la contraseña si se proporciona
            if ($request->filled('password')) {
                $data['password'] = \Hash::make($request->password);
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
     * Enviar notificación al administrador cuando se crea un nuevo préstamo
     * @param WarehouseMovement $loanMovement
     * @return void
     */
    private function notifyAdminNewLoan($loanMovement)
    {
        try {
            \Log::info('notifyAdminNewLoan: Iniciando notificación para préstamo ID: ' . $loanMovement->id);
            
            // Buscar usuarios con roles de administrador
            $adminRoleIds = [1, 5, 7, 16, 19, 24, 30, 38];
            $admins = User::whereHas('roles', function($query) use ($adminRoleIds) {
                $query->whereIn('roles.id', $adminRoleIds);
            })->get();

            \Log::info('notifyAdminNewLoan: Administradores encontrados por IDs: ' . $admins->count());

            if ($admins->isEmpty()) {
                $admins = User::whereHas('roles', function($query) {
                    $query->where('name', 'Administrador')
                          ->orWhere('name', 'Super Administrador');
                })->get();
                \Log::info('notifyAdminNewLoan: Administradores encontrados por nombre: ' . $admins->count());
            }

            if ($admins->isEmpty()) {
                // Si no hay administradores con esos roles, buscar por slug
                $admins = User::whereHas('roles', function($query) {
                    $query->where('slug', 'infrastock.admin');
                })->get();
                \Log::info('notifyAdminNewLoan: Administradores encontrados por slug: ' . $admins->count());
            }

            if ($admins->isEmpty()) {
                \Log::warning('No se encontraron administradores para notificar sobre el nuevo préstamo');
                return;
            }

            $toolName = $loanMovement->tool ? ($loanMovement->tool->nombre ?? $loanMovement->tool->name ?? 'Herramienta') : 'Herramienta';
            $userName = $loanMovement->user ? ($loanMovement->user->nickname ?? $loanMovement->user->name ?? 'Instructor') : 'Instructor';
            $amount = $loanMovement->amount ? " ({$loanMovement->amount} unidades)" : '';
            $purpose = $loanMovement->purpose ? " - Finalidad: " . substr($loanMovement->purpose, 0, 50) . (strlen($loanMovement->purpose) > 50 ? '...' : '') : '';
            $requiredDate = $loanMovement->required_date ? " - Fecha requerida: " . \Carbon\Carbon::parse($loanMovement->required_date)->format('d/m/Y') : '';

            foreach ($admins as $admin) {
                try {
                    \Log::info('notifyAdminNewLoan: Creando notificación para admin ID: ' . $admin->id);
                    
                    $notification = Notification::create([
                        'type' => 'loan_created',
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id' => $admin->id,
                        'data' => [
                            'title' => 'Nuevo Préstamo de Herramienta',
                            'message' => "El instructor {$userName} ha solicitado el préstamo de la herramienta: {$toolName}{$amount}{$purpose}{$requiredDate}.",
                            'tool_id' => $loanMovement->tool ? $loanMovement->tool->id : null,
                            'tool_name' => $toolName,
                            'loan_id' => $loanMovement->id,
                            'user_id' => $loanMovement->user_id,
                            'user_name' => $userName,
                            'amount' => $loanMovement->amount,
                            'purpose' => $loanMovement->purpose,
                            'required_date' => $loanMovement->required_date,
                            'created_at' => now()->format('d/m/Y H:i'),
                            'action_url' => route('infrastock.admin.loans.index'),
                        ],
                        'read_at' => null,
                    ]);
                    
                    \Log::info('Notificación de nuevo préstamo creada exitosamente. ID: ' . $notification->id . ' para admin ID: ' . $admin->id);
                } catch (\Exception $e) {
                    \Log::error('Error al crear notificación para administrador ID ' . $admin->id . ': ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error en notifyAdminNewLoan: ' . $e->getMessage());
        }
    }

    /**
     * Cierra la sesión del instructor.
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
}

