<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Labor;
use Modules\INFRASTOCK\Entities\Inventory;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use App\Models\User;

/**
 * @class SupplyController
 * @brief Controlador para la gestión de Insumos en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD para los equipos/insumos.
 * Permite registrar, visualizar, actualizar y eliminar insumos.
 * Utiliza modales para la creación y edición de insumos para una experiencia de usuario optimizada.
 */
class SupplyController extends Controller
{
    /**
     * Muestra una lista de todos los insumos registrados.
     * Carga las relaciones de categoría, labor e inventario para cada insumo.
     * También obtiene categorías de tipo 'supply', labores e inventarios para poblar los selectores en los modales.
     * @return Renderable
     */
    public function index(Request $request)
    {
        // Verificar y crear notificaciones para insumos próximos a vencer
        // Solo ejecutar si no hay un filtro específico de equipment_id (para evitar demoras en redirecciones desde notificaciones)
        // Usar cache para evitar ejecutar en cada carga (cada 5 minutos)
        if (!$request->has('filter_equipment_id')) {
            $cacheKey = 'check_expiring_supplies_' . auth()->id();
            if (!\Cache::has($cacheKey)) {
                $this->checkExpiringSupplies();
                \Cache::put($cacheKey, true, now()->addMinutes(5)); // Cache por 5 minutos
            }
        }
        
        // Obtener todos los insumos (DataTables manejará la paginación)
        $query = Equipment::with('category', 'labor', 'inventory');
        
        // Si hay un filtro por equipment_id (desde notificación), aplicar filtro
        if ($request->has('filter_equipment_id') && $request->filter_equipment_id) {
            $query->where('id', $request->filter_equipment_id);
        }
        
        // Si hay un filtro para insumos próximos a vencer, aplicar filtro
        if ($request->has('filter_expiring') && $request->filter_expiring) {
            $query->whereNotNull('expiration_date')
                  ->where('expiration_date', '>', now())
                  ->where('expiration_date', '<=', now()->addDays(30));
        }
        
        $supplies = $query->get(); // Obtiene todos los insumos (sin paginación de Laravel, DataTables lo manejará).
        $categories = InfrastockCategory::where('type', 'supply')->get(); // Obtiene categorías específicas para insumos.
        $labors = Labor::all(); // Obtiene todas las labores.
        $inventories = Inventory::all(); // Obtiene todos los inventarios.
        $users = User::with('person')->get(); // Obtiene todos los usuarios para préstamos con relación person.
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get(); // Obtiene unidades productivas/almacenes.
        
        // Retorna la vista index de insumos con todos los datos necesarios.
        return view('infrastock::admin.supplies.index', compact('supplies', 'categories', 'labors', 'inventories', 'users', 'productiveUnitWarehouses'));
    }
    
    /**
     * Verifica insumos próximos a vencer y crea notificaciones.
     * @return void
     */
    private function checkExpiringSupplies()
    {
        $user = auth()->user();
        if (!$user) {
            \Log::info('checkExpiringSupplies: Usuario no autenticado');
            return;
        }
        
        // Verificar si el usuario es administrador - verificar también por slug
        $userRoles = $user->roles->pluck('name')->toArray();
        $userSlugs = $user->roles->pluck('slug')->toArray();
        $isAdmin = in_array('Administrador', $userRoles) || in_array('infrastock.admin', $userSlugs);
        
        \Log::info('checkExpiringSupplies: Usuario ID ' . $user->id . ', Roles: ' . implode(', ', $userRoles) . ', Slugs: ' . implode(', ', $userSlugs) . ', Es Admin: ' . ($isAdmin ? 'Sí' : 'No'));
        
        if (!$isAdmin) {
            \Log::info('checkExpiringSupplies: Usuario no es administrador, saliendo');
            return; // Solo crear notificaciones para administradores
        }
        
        // Obtener insumos que están próximos a vencer (30 días o menos) y no han vencido
        $expiringSupplies = Equipment::whereNotNull('expiration_date')
            ->where('expiration_date', '>', now())
            ->where('expiration_date', '<=', now()->addDays(30))
            ->get();
        
        \Log::info('checkExpiringSupplies: Encontrados ' . $expiringSupplies->count() . ' insumos próximos a vencer');
        
        // Optimización: Obtener todas las notificaciones de una vez para evitar N+1 queries
        $allNotifications = \Modules\INFRASTOCK\Entities\Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->where('type', 'supply_expiring')
            ->get();
        
        // Separar notificaciones leídas y no leídas
        $unreadNotifications = $allNotifications->whereNull('read_at')->keyBy(function($notification) {
            return $notification->data['equipment_id'] ?? null;
        });
        
        $readNotifications = $allNotifications->whereNotNull('read_at')
            ->groupBy(function($notification) {
                return $notification->data['equipment_id'] ?? null;
            })
            ->map(function($group) {
                return $group->sortByDesc('read_at')->first();
            });
        
        foreach ($expiringSupplies as $supply) {
            $daysUntilExpiration = now()->diffInDays($supply->expiration_date, false);
            
            // Verificar si ya existe una notificación NO LEÍDA para este insumo (usando colección en memoria)
            $existingNotification = $unreadNotifications->get($supply->id);
            
            // Si no existe una notificación no leída, verificar si hay una leída para recrearla
            // La frecuencia depende de cuántos días faltan para vencer:
            // - Si faltan 10 días o menos: recrear diariamente
            // - Si faltan más de 10 días: recrear cada 7 días
            if (!$existingNotification) {
                $lastReadNotification = $readNotifications->get($supply->id);
                
                // Determinar la frecuencia de recreación según los días restantes
                $daysUntilExpiration = now()->diffInDays($supply->expiration_date, false);
                $recreateInterval = $daysUntilExpiration <= 10 ? 1 : 7; // Diario si <= 10 días, cada 7 días si > 10 días
                
                // Si no hay notificación leída, o la última fue leída hace más del intervalo correspondiente, crear una nueva
                $shouldCreate = !$lastReadNotification || 
                               $lastReadNotification->read_at->diffInDays(now()) >= $recreateInterval;
                
                if ($shouldCreate) {
                    try {
                        // Crear notificación
                        $notification = new \Modules\INFRASTOCK\Entities\Notification();
                        $notification->id = \Illuminate\Support\Str::uuid()->toString();
                        $notification->type = 'supply_expiring';
                        $notification->notifiable_type = 'App\Models\User';
                        $notification->notifiable_id = $user->id;
                        $notification->data = [
                            'title' => 'Insumo próximo a vencer',
                            'message' => "El insumo '{$supply->name}' vence en {$daysUntilExpiration} día(s). Fecha: " . $supply->expiration_date->format('d/m/Y'),
                            'equipment_id' => $supply->id,
                            'expiration_date' => $supply->expiration_date->format('Y-m-d'),
                            'days_until_expiration' => $daysUntilExpiration,
                            'action_url' => route('infrastock.admin.supplies.index'),
                        ];
                        $notification->read_at = null;
                        $notification->save();
                        \Log::info('checkExpiringSupplies: Notificación creada para insumo ' . $supply->id . ' (' . $supply->name . ') con ID: ' . $notification->id);
                    } catch (\Exception $e) {
                        \Log::error('checkExpiringSupplies: Error al crear notificación: ' . $e->getMessage());
                    }
                } else {
                    \Log::info('checkExpiringSupplies: Ya existe notificación reciente (leída hace ' . $lastReadNotification->read_at->diffInDays(now()) . ' días) para insumo ' . $supply->id);
                }
            } else {
                \Log::info('checkExpiringSupplies: Ya existe notificación no leída para insumo ' . $supply->id);
            }
        }
    }

    /**
     * Muestra el formulario para crear un nuevo insumo.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.supplies.index');
    }

    /**
     * Almacena un nuevo insumo en la base de datos.
     * Realiza validación de los datos antes del almacenamiento.
     * @param Request $request La solicitud HTTP que contiene los datos del insumo.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'name' => 'required|string|max:255', // Nombre del insumo es obligatorio y máximo 255 caracteres.
            'category_id' => 'required|exists:infrastock_categories,id', // ID de categoría es obligatorio y debe existir.
            'characteristics' => 'nullable|string', // Características es opcional.
            'initial_amount' => 'required|integer|min:0', // Cantidad inicial es obligatoria, entera y mínimo 0.
            'minimum_stock' => 'nullable|integer|min:0', // Valor mínimo permitido es opcional, entero y mínimo 0.
            'unit_measure' => 'nullable|string|max:50', // Unidad de medida es opcional.
            'observations' => 'nullable|string', // Observaciones es opcional.
            // Campos opcionales para INFRASTOCK (otros sistemas pueden requerirlos)
            'inventory_id' => 'nullable|exists:inventories,id', // ID de inventario es opcional para INFRASTOCK.
            'labor_id' => 'nullable|exists:labors,id', // ID de labor es opcional para INFRASTOCK.
            'price' => 'nullable|numeric|min:0', // Precio es opcional para INFRASTOCK.
            'expiration_date' => 'nullable|date', // Fecha de vencimiento es opcional.
        ]);

        try {
            // Preparar datos para crear, asegurando que los campos opcionales sean null si no se proporcionan
            $data = $request->only([
                'name', 'category_id', 'characteristics', 'initial_amount', 
                'minimum_stock', 'observations', 'expiration_date'
            ]);
            
            // Manejar unidad de medida: si viene unit_measure_final, usarlo; si no, usar unit_measure
            $unitMeasure = $request->input('unit_measure_final') ?: $request->input('unit_measure');
            if ($unitMeasure === 'otro') {
                $data['unit_measure'] = $request->input('unit_measure_other') ?: null;
            } else {
                $data['unit_measure'] = $unitMeasure ?: null;
            }
            
            // Campos opcionales que pueden ser null para INFRASTOCK
            // Como labor_id e inventory_id son NOT NULL en la BD, usamos valores por defecto si no se proporcionan
            if ($request->input('labor_id')) {
                $data['labor_id'] = $request->input('labor_id');
            } else {
                // Obtener el primer labor disponible como valor por defecto
                $defaultLabor = \Modules\INFRASTOCK\Entities\Labor::first();
                if ($defaultLabor) {
                    $data['labor_id'] = $defaultLabor->id;
                } else {
                    return redirect()->route('infrastock.admin.supplies.index')
                        ->with('error', 'No hay labores disponibles. Por favor, contacte al administrador.')
                        ->withInput();
                }
            }
            
            if ($request->input('inventory_id')) {
                $data['inventory_id'] = $request->input('inventory_id');
            } else {
                // Obtener el primer inventario disponible como valor por defecto
                $defaultInventory = \Modules\INFRASTOCK\Entities\Inventory::first();
                if ($defaultInventory) {
                    $data['inventory_id'] = $defaultInventory->id;
                } else {
                    return redirect()->route('infrastock.admin.supplies.index')
                        ->with('error', 'No hay inventarios disponibles. Por favor, contacte al administrador.')
                        ->withInput();
                }
            }
            
            $data['price'] = $request->input('price') ?: 0; // Precio por defecto 0 si no se proporciona
            $data['amount'] = $request->input('initial_amount', 0); // amount se iguala a initial_amount
            
            Equipment::create($data); // Crea un nuevo insumo con los datos validados.
        } catch (\Illuminate\Database\QueryException $e) {
            // Si hay error de duplicado en la base de datos
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.supplies.index')
                    ->with('error', 'Ya existe un insumo con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e; // Re-lanzar otros errores de base de datos
        }

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supplies.index')->with('success', 'Insumo registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un insumo específico.
     * @param int $id El ID del insumo.
     * @return Renderable
     */
    public function show($id)
    {
        $supply = Equipment::with('category', 'labor', 'inventory')->findOrFail($id); // Encuentra el insumo con sus relaciones.
        return view('infrastock::admin.supplies.show', compact('supply')); // Retorna la vista show con los detalles del insumo.
    }

    /**
     * Muestra el formulario para editar un insumo específico.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID del insumo.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.supplies.index');
    }

    /**
     * Actualiza un insumo existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados del insumo.
     * @param int $id El ID del insumo a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:infrastock_categories,id',
            'characteristics' => 'nullable|string',
            'initial_amount' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'unit_measure' => 'nullable|string|max:50',
            'observations' => 'nullable|string',
            // Campos opcionales para INFRASTOCK (otros sistemas pueden requerirlos)
            'inventory_id' => 'nullable|exists:inventories,id',
            'labor_id' => 'nullable|exists:labors,id',
            'price' => 'nullable|numeric|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        $supply = Equipment::findOrFail($id); // Encuentra el insumo por su ID o lanza una excepción.
        
        try {
            // Preparar datos para actualizar, asegurando que los campos opcionales sean null si no se proporcionan
            $data = $request->only([
                'name', 'category_id', 'characteristics', 'initial_amount', 
                'minimum_stock', 'observations', 'expiration_date'
            ]);
            
            // Manejar unidad de medida: si viene unit_measure_final, usarlo; si no, usar unit_measure
            $unitMeasure = $request->input('unit_measure_final') ?: $request->input('unit_measure');
            if ($unitMeasure === 'otro') {
                $data['unit_measure'] = $request->input('unit_measure_other') ?: null;
            } else {
                $data['unit_measure'] = $unitMeasure ?: null;
            }
            
            // Campos opcionales que pueden ser null para INFRASTOCK
            // Como labor_id e inventory_id son NOT NULL en la BD, mantenemos los valores actuales si no se proporcionan
            if ($request->has('labor_id') && $request->input('labor_id')) {
                $data['labor_id'] = $request->input('labor_id');
            } else {
                // Mantener el valor actual del insumo
                $data['labor_id'] = $supply->labor_id;
            }
            
            if ($request->has('inventory_id') && $request->input('inventory_id')) {
                $data['inventory_id'] = $request->input('inventory_id');
            } else {
                // Mantener el valor actual del insumo
                $data['inventory_id'] = $supply->inventory_id;
            }
            
            // Precio: mantener el actual si no se proporciona
            if ($request->has('price')) {
                $data['price'] = $request->input('price') ?: 0;
            } else {
                $data['price'] = $supply->price ?? 0;
            }
            
            $data['amount'] = $request->input('initial_amount', $supply->amount); // amount se iguala a initial_amount
            
            $supply->update($data); // Actualiza el insumo con los nuevos datos.
            
            // Limpiar cache de notificaciones para que se actualicen si cambió la fecha de vencimiento
            \Cache::forget('check_expiring_supplies_' . auth()->id());
            \Cache::forget('check_expiring_supplies_dashboard_' . auth()->id());
        } catch (\Illuminate\Database\QueryException $e) {
            // Si hay error de duplicado en la base de datos
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.supplies.index')
                    ->with('error', 'Ya existe un insumo con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e; // Re-lanzar otros errores de base de datos
        }

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supplies.index')->with('success', 'Insumo actualizado exitosamente.');
    }

    /**
     * Elimina un insumo de la base de datos.
     * @param int $id El ID del insumo a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $supply = Equipment::findOrFail($id); // Encuentra el insumo por su ID o lanza una excepción.
        
        // Por ahora, permitir eliminar todos los insumos sin validación
        // TODO: Implementar validación cuando se definan las relaciones correctas
        $hasRelatedRecords = false; // Temporalmente deshabilitado
        
        if ($hasRelatedRecords) {
            // Si es una petición AJAX, devolver respuesta JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el insumo porque tiene registros relacionados.'
                ], 422);
            }

            // Redirige a la vista index con un parámetro de error para SweetAlert2
            return redirect()->route('infrastock.admin.supplies.index')->with('error', 'No se puede eliminar el insumo porque tiene registros relacionados.');
        }
        
        $supply->delete(); // Elimina el insumo de la base de datos (soft delete si está configurado).

        // Limpiar cache de notificaciones al eliminar un insumo
        \Cache::forget('check_expiring_supplies_' . auth()->id());
        \Cache::forget('check_expiring_supplies_dashboard_' . auth()->id());

        // Si es una petición AJAX o tiene el header X-Requested-With, devolver respuesta JSON
        if (request()->ajax() || request()->wantsJson() || request()->hasHeader('X-Requested-With')) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo eliminado exitosamente.'
            ]);
        }

        // Redirige a la vista index con un parámetro de éxito para SweetAlert2
        return redirect()->route('infrastock.admin.supplies.index')->with('success', 'deleted');
    }

    /**
     * Almacena un nuevo préstamo de insumo desde el perfil del admin.
     * Los préstamos no cuentan como consumo ya que regresan a bodega.
     * @param Request $request La solicitud HTTP que contiene los datos del préstamo.
     * @return Renderable
     */
    public function storeLoan(Request $request)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'borrower_name' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'loan_location' => 'required|string|max:255',
            'loan_date' => 'required|date',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        // Verificar que hay stock suficiente
        if (!$equipment->hasStockFor($request->amount)) {
            return redirect()->route('infrastock.admin.supplies.index')
                ->with('error', 'No hay stock suficiente para realizar el préstamo. Stock disponible: ' . $equipment->stock)
                ->withInput();
        }

        try {
            // Obtener la primera unidad productiva/almacén disponible (o usar la del equipo)
            $productiveUnitWarehouse = ProductiveUnitWarehouse::first();
            if (!$productiveUnitWarehouse) {
                return redirect()->route('infrastock.admin.supplies.index')
                    ->with('error', 'No se encontró una unidad productiva/almacén configurada.')
                    ->withInput();
            }

            // Usar el usuario actual (admin) como user_id, pero guardar el nombre del prestatario en la descripción
            $borrowerName = $request->borrower_name;
            $adminUserId = auth()->id();

            // Crear el movimiento de préstamo
            // Nota: Los préstamos se registran con role='Préstamo' pero para descontar del stock
            // también creamos un movimiento 'Entrega' que sí afecta el cálculo de stock
            // El movimiento 'Préstamo' sirve para identificar que es un préstamo que regresará
            WarehouseMovement::create([
                'productive_unit_warehouse_id' => $productiveUnitWarehouse->id,
                'equipment_id' => $equipment->id,
                'item_type' => 'equipment',
                'user_id' => $adminUserId, // Usuario admin que registra el préstamo
                'role' => 'Préstamo',
                'amount' => $request->amount,
                'description' => 'Préstamo - Prestatario: ' . $borrowerName . ' | Lugar: ' . $request->loan_location . ' | Fecha: ' . $request->loan_date,
            ]);

            // Crear también un movimiento 'Entrega' para descontar del stock
            // Esto permite que el stock se descuente correctamente
            WarehouseMovement::create([
                'productive_unit_warehouse_id' => $productiveUnitWarehouse->id,
                'equipment_id' => $equipment->id,
                'item_type' => 'equipment',
                'user_id' => $adminUserId, // Usuario admin que registra el préstamo
                'role' => 'Entrega',
                'amount' => $request->amount,
                'description' => 'Préstamo (regresa a bodega) - Prestatario: ' . $borrowerName . ' | Lugar: ' . $request->loan_location . ' | Fecha: ' . $request->loan_date,
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.supplies.index')
                    ->with('error', 'Ya existe un préstamo con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }

        // Redirige a la vista de préstamos de insumos con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supplies.loans.index')->with('success', 'Préstamo registrado exitosamente. El stock ha sido descontado.');
    }

    /**
     * Muestra una lista de todos los préstamos de insumos registrados por el admin.
     * @return Renderable
     */
    public function indexLoans()
    {
        // Obtener solo préstamos de insumos (equipment) registrados por el admin
        $loans = WarehouseMovement::with(['user.person', 'equipment.category', 'productiveUnitWarehouse.productiveUnit', 'productiveUnitWarehouse.warehouse'])
                                ->where('role', 'Préstamo')
                                ->where('item_type', 'equipment')
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);

        // Para cada préstamo, verificar si ya fue devuelto
        $loans->getCollection()->transform(function($loan) {
            $existingReturn = WarehouseMovement::where('equipment_id', $loan->equipment_id)
                                              ->where('user_id', $loan->user_id)
                                              ->where('role', 'Recibe')
                                              ->where('item_type', 'equipment')
                                              ->where('amount', $loan->amount)
                                              ->where('description', 'like', '%Devolución de préstamo #' . $loan->id . '%')
                                              ->first();
            
            $loan->is_returned = $existingReturn ? true : false;
            $loan->return_movement = $existingReturn;
            
            // Extraer fecha de devolución de la descripción del préstamo
            if ($loan->is_returned && preg_match('/DEVUELTO:\s*(.+?)(?:\s*\|)?$/', $loan->description ?? '', $matches)) {
                $loan->return_date = trim($matches[1]);
            } elseif ($existingReturn && preg_match('/Fecha y hora:\s*(.+?)$/', $existingReturn->description ?? '', $matches)) {
                $loan->return_date = trim($matches[1]);
            } else {
                $loan->return_date = null;
            }
            
            return $loan;
        });

        $equipments = Equipment::with('category')->get();
        $users = User::with('person')->get();
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get();

        return view('infrastock::admin.supplies.loans.index', compact('loans', 'equipments', 'users', 'productiveUnitWarehouses'));
    }

    /**
     * Procesa la devolución de un préstamo de insumo.
     * Registra la fecha y hora de devolución y crea un movimiento 'Recibe' para aumentar el stock.
     * @param int $id El ID del préstamo (WarehouseMovement).
     * @return Renderable
     */
    public function returnLoan($id)
    {
        $loan = WarehouseMovement::with(['equipment', 'user', 'productiveUnitWarehouse'])
                                ->where('id', $id)
                                ->where('role', 'Préstamo')
                                ->where('item_type', 'equipment')
                                ->first();

        if (!$loan) {
            return redirect()->route('infrastock.admin.supplies.loans.index')
                ->with('error', 'Préstamo no encontrado.');
        }

        // Verificar si ya fue devuelto (buscando un movimiento 'Recibe' relacionado)
        $existingReturn = WarehouseMovement::where('equipment_id', $loan->equipment_id)
                                          ->where('user_id', $loan->user_id)
                                          ->where('role', 'Recibe')
                                          ->where('item_type', 'equipment')
                                          ->where('amount', $loan->amount)
                                          ->where('description', 'like', '%Devolución de préstamo #' . $loan->id . '%')
                                          ->first();

        if ($existingReturn) {
            return redirect()->route('infrastock.admin.supplies.loans.index')
                ->with('error', 'Este préstamo ya fue devuelto anteriormente.');
        }

        try {
            $returnDate = now();
            $returnDateTime = $returnDate->format('d/m/Y H:i:s');

            // Actualizar la descripción del préstamo para incluir la información de devolución
            $loan->update([
                'description' => ($loan->description ?? '') . ' | DEVUELTO: ' . $returnDateTime,
            ]);

            // Crear un movimiento 'Recibe' para aumentar el stock
            // Esto permite que el stock se incremente correctamente
            WarehouseMovement::create([
                'productive_unit_warehouse_id' => $loan->productive_unit_warehouse_id,
                'equipment_id' => $loan->equipment_id,
                'item_type' => 'equipment',
                'user_id' => $loan->user_id,
                'role' => 'Recibe',
                'amount' => $loan->amount,
                'description' => 'Devolución de préstamo #' . $loan->id . ' - Fecha y hora: ' . $returnDateTime,
            ]);

            return redirect()->route('infrastock.admin.supplies.loans.index')
                ->with('success', 'Devolución registrada exitosamente. El stock ha sido actualizado. Fecha y hora: ' . $returnDateTime);
        } catch (\Exception $e) {
            return redirect()->route('infrastock.admin.supplies.loans.index')
                ->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }
}
