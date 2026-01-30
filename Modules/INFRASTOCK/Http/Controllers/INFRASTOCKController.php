<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\ProductiveUnit;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use App\Models\User;
use Carbon\Carbon;

/**
 * @class INFRASTOCKController
 * @brief Controlador principal para el módulo INFRASTOCK.
 *
 * Este controlador maneja las vistas principales del módulo, como la página de inicio
 * y el panel de administración (dashboard). También contiene métodos placeholder
 * para operaciones CRUD genéricas a nivel de módulo que no están siendo utilizadas
 * activamente o han sido delegadas a controladores específicos (ej. AreaController).
 */
class INFRASTOCKController extends Controller
{
    /**
     * Muestra la página de inicio del módulo INFRASTOCK.
     * Esta vista suele ser el punto de entrada público o de bienvenida del módulo.
     * @return Renderable
     */
    public function index()
    {
        return view('infrastock::index'); // Retorna la vista 'index.blade.php' del módulo INFRASTOCK.
    }


    /**
     * Muestra el formulario para crear un nuevo recurso a nivel de módulo.
     * Actualmente, esta función actúa como un placeholder y redirige a la vista principal
     * ya que las operaciones de creación se manejan a través de modales en vistas específicas (ej. áreas, insumos).
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('cefa.infrastock.index'); // Redirige a la página principal del módulo.
    }

    /**
     * Almacena un recurso recién creado a nivel de módulo.
     * Actualmente, este método es un placeholder y no implementa ninguna lógica de almacenamiento.
     * Las operaciones de almacenamiento se delegan a controladores específicos.
     * @param Request $request La solicitud HTTP.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Este método es un placeholder. La lógica de almacenamiento se maneja en controladores específicos.
    }

    /**
     * Muestra el panel de administración (dashboard) del módulo INFRASTOCK.
     * Recopila y presenta datos estadísticos y en tiempo real sobre inventarios,
     * movimientos, solicitudes y herramientas prestadas.
     * @return Renderable
     */
    public function dashboard()
    {
        // Datos para las tarjetas de información en el dashboard:

        // Conteo de nuevas solicitudes de insumos pendientes (no solo de los últimos 7 días, sino todas las pendientes).
        $newSupplyRequestsCount = \Modules\INFRASTOCK\Entities\Request::where('status', 'pending')
                                                ->where('created_at', '>=', Carbon::now()->subDays(30))
                                                ->count();

        // Calcula el stock total disponible usando el nuevo sistema.
        $totalStockAmount = Equipment::get()->sum('stock');
        // Suma total de la cantidad inicial de todos los insumos.
        $totalInitialAmount = Equipment::sum('initial_amount') ?: Equipment::sum('amount');
        // Calcula el porcentaje de insumos en stock respecto al total inicial.
        $suppliesPercentage = ($totalInitialAmount > 0) ? round(($totalStockAmount / $totalInitialAmount) * 100, 2) : 0;
        
        // Si el porcentaje es mayor a 100 o menor a 0, mostrar la cantidad total en lugar del porcentaje
        if ($suppliesPercentage > 100 || $suppliesPercentage < 0) {
            $suppliesPercentage = $totalStockAmount;
        }

        // Conteo de herramientas que actualmente están en préstamo.
        // Buscar préstamos activos (sin devolución)
        $toolsOnLoanCount = WarehouseMovement::where('item_type', 'tool')
                                            ->whereIn('role', ['Préstamo', 'Prestamo'])
                                            ->whereNull('deleted_at')
                                            ->count();

        // Conteo de insumos próximos a vencer (con fecha de vencimiento dentro de los próximos 30 días).
        // Incluir también los que ya vencieron recientemente (últimos 7 días) para alertar
        $expiringSuppliesCount = Equipment::whereNotNull('expiration_date')
                                        ->where('expiration_date', '<=', Carbon::now()->addDays(30))
                                        ->where('expiration_date', '>=', Carbon::now()->subDays(7))
                                        ->count();

        // Datos para el gráfico de Consumo de Insumos por Área:
        // Realiza un join con las tablas 'productive_unit_warehouses' y 'productive_units' para agrupar
        // el consumo por el nombre del área productiva.
        $consumptionByArea = WarehouseMovement::selectRaw('SUM(amount) as total_amount, productive_units.name as area_name')
            ->join('productive_unit_warehouses', 'warehouse_movements.productive_unit_warehouse_id', '=', 'productive_unit_warehouses.id')
            ->join('productive_units', 'productive_unit_warehouses.productive_unit_id', '=', 'productive_units.id')
            ->where('warehouse_movements.item_type', 'equipment')
            ->groupBy('productive_units.name')
            ->get();

        // Extrae los nombres de las áreas y las cantidades de consumo para el gráfico.
        $areaNames = $consumptionByArea->pluck('area_name')->toArray();
        $consumptionAmounts = $consumptionByArea->pluck('total_amount')->toArray();

        // Datos para el gráfico de uso de Herramientas por Instructor:
        // Realiza un join con la tabla 'users' para agrupar los préstamos por el apodo (nickname) del usuario (instructor).
        $toolsByInstructor = WarehouseMovement::selectRaw('COUNT(warehouse_movements.id) as total_loans, users.nickname as user_name')
            ->join('users', 'warehouse_movements.user_id', '=', 'users.id')
            ->where('warehouse_movements.item_type', 'tool')
            ->where('warehouse_movements.role', 'Préstamo')
            ->groupBy('users.nickname')
            ->get();
        
        // Extrae los nombres de los instructores y el conteo de préstamos para el gráfico.
        $instructorNames = $toolsByInstructor->pluck('user_name')->toArray();
        $loanCounts = $toolsByInstructor->pluck('total_loans')->toArray();

        // Verificar y crear notificaciones para insumos próximos a vencer
        // Usar cache para evitar ejecutar en cada carga (cada 5 minutos)
        $cacheKey = 'check_expiring_supplies_dashboard_' . auth()->id();
        if (!\Cache::has($cacheKey)) {
            $this->checkExpiringSupplies();
            \Cache::put($cacheKey, true, now()->addMinutes(5)); // Cache por 5 minutos
        }
        
        // Cargar notificaciones para el usuario actual
        $notifications = \Modules\INFRASTOCK\Entities\Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', auth()->id())
            ->whereIn('type', ['request_created', 'request_approved', 'request_rejected', 'supply_expiring', 'loan_created', 'loan_approved', 'loan_rejected'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();

        $notificationCount = $notifications->where('read_at', null)->count();

        // Retorna la vista del dashboard con todos los datos recopilados.
        return view('infrastock::admin.dashboard', compact(
            'newSupplyRequestsCount',
            'suppliesPercentage',
            'toolsOnLoanCount',
            'expiringSuppliesCount',
            'areaNames',
            'consumptionAmounts',
            'instructorNames',
            'loanCounts',
            'notifications',
            'notificationCount'
        ));
    }

    /**
     * Muestra el formulario para editar un recurso a nivel de módulo.
     * Actualmente, esta función actúa como un placeholder y redirige a la vista principal
     * ya que las operaciones de edición se manejan a través de modales en vistas específicas.
     * @param int $id El ID del recurso a editar (no utilizado activamente).
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('cefa.infrastock.index'); // Redirige a la página principal del módulo.
    }

    /**
     * Actualiza un recurso existente a nivel de módulo.
     * Actualmente, este método es un placeholder y no implementa ninguna lógica de actualización.
     * Las operaciones de actualización se delegan a controladores específicos.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados.
     * @param int $id El ID del recurso a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Este método es un placeholder. La lógica de actualización se maneja en controladores específicos.
    }

    /**
     * Elimina un recurso existente a nivel de módulo.
     * Actualmente, este método es un placeholder y no implementa ninguna lógica de eliminación.
     * Las operaciones de eliminación se delegan a controladores específicos.
     * @param int $id El ID del recurso a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        // Este método es un placeholder. La lógica de eliminación se maneja en controladores específicos.
    }

    /**
     * Maneja la lógica posterior al inicio de sesión para el módulo INFRASTOCK.
     * Redirige al usuario autenticado al dashboard correspondiente según su rol.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postlogin(){
        // Igual que el admin, simplemente redirige al dashboard correspondiente
        // Sin lógica compleja que pueda causar bucles
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuario no autenticado');
        }

        // Obtener los roles del usuario
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Log para debugging
        \Log::info('Post-login - Usuario: ' . $user->id . ' (' . $user->name . '), Roles: ' . implode(', ', $userRoles));
        
        // Verificar el rol del usuario y redirigir al dashboard correspondiente
        // Orden importante: verificar roles específicos en orden de prioridad
        // Aseo debe verificarse ANTES que Operario para evitar conflictos
        
        if (in_array('Aseo', $userRoles) || in_array('Personal de Aseo', $userRoles)) {
            \Log::info('Redirigiendo a Personal de Aseo dashboard');
            return redirect()->route('infrastock.cleaning-staff.dashboard');
        } elseif (in_array('Psicola', $userRoles)) {
            \Log::info('Redirigiendo a Psicola dashboard');
            return redirect()->route('infrastock.psicola.dashboard');
        } elseif (in_array('Ciencias Basicas', $userRoles)) {
            \Log::info('Redirigiendo a Ciencias Basicas dashboard');
            return redirect()->route('infrastock.ciencias-basicas.dashboard');
        } elseif (in_array('Operario', $userRoles)) {
            \Log::info('Redirigiendo a Operario dashboard');
            return redirect()->route('infrastock.operator.dashboard');
        } elseif (in_array('Centro de Convivencia', $userRoles)) {
            \Log::info('Redirigiendo a Centro de Convivencia dashboard');
            return redirect()->route('infrastock.convivencia.dashboard');
        } elseif (in_array('Ganaderia', $userRoles)) {
            \Log::info('Redirigiendo a Ganaderia dashboard');
            return redirect()->route('infrastock.ganaderia.dashboard');
        } elseif (in_array('Vigilancia', $userRoles)) {
            \Log::info('Redirigiendo a Vigilancia dashboard');
            return redirect()->route('infrastock.vigilancia.dashboard');
        } elseif (in_array('Agroindustria', $userRoles)) {
            \Log::info('Redirigiendo a Agroindustria dashboard');
            return redirect()->route('infrastock.agroindustria.dashboard');
        } elseif (in_array('Instructor', $userRoles)) {
            \Log::info('Redirigiendo a Instructor dashboard');
            return redirect()->route('infrastock.instructor.dashboard');
        } else {
            // Para administradores o usuarios sin rol específico
            \Log::info('Redirigiendo a dashboard de administrador (no se encontró rol específico)');
            return redirect()->route('cefa.infrastock.admin.dashboard');
        }
    }

    /**
     * Cierra la sesión del usuario administrador.
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
     * Verifica insumos próximos a vencer y crea notificaciones.
     * @return void
     */
    private function checkExpiringSupplies()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }
        
        // Verificar si el usuario es administrador - verificar también por slug
        $userRoles = $user->roles->pluck('name')->toArray();
        $userSlugs = $user->roles->pluck('slug')->toArray();
        $isAdmin = in_array('Administrador', $userRoles) || in_array('infrastock.admin', $userSlugs);
        
        if (!$isAdmin) {
            return; // Solo crear notificaciones para administradores
        }
        
        // Obtener insumos que están próximos a vencer (30 días o menos) y no han vencido
        $expiringSupplies = Equipment::whereNotNull('expiration_date')
            ->where('expiration_date', '>', now())
            ->where('expiration_date', '<=', now()->addDays(30))
            ->get();
        
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
                        \Log::info('checkExpiringSupplies (INFRASTOCKController): Notificación creada para insumo ' . $supply->id . ' (' . $supply->name . ') con ID: ' . $notification->id);
                    } catch (\Exception $e) {
                        \Log::error('checkExpiringSupplies (INFRASTOCKController): Error al crear notificación: ' . $e->getMessage());
                    }
                }
            }
        }
    }
}
