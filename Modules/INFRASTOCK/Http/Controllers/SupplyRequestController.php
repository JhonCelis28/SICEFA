<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\INFRASTOCK\Exports\SupplyConsumptionExport;
use Carbon\Carbon;

/**
 * @class SupplyRequestController
 * @brief Controlador para la gestión de Solicitudes de Insumos en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD para las solicitudes de insumos realizadas
 * por los usuarios. Permite al administrador visualizar, gestionar (cambiar estado) y eliminar
 * estas solicitudes. La creación y edición se gestionan a través de modales para una
 * experiencia de usuario fluida.
 */
class SupplyRequestController extends Controller
{
    /**
     * Muestra una lista de todas las solicitudes de insumos pendientes.
     * Carga las relaciones con el usuario solicitante, el insumo solicitado y la unidad
     * productiva/almacén asociada para mostrar detalles completos en la tabla.
     * También obtiene listas de insumos, usuarios y unidades productivas/almacenes
     * para poblar los selectores en los modales de gestión.
     * @return Renderable
     */
    public function index()
    {
        // Obtiene las solicitudes del nuevo sistema con estado 'pending' y sus relaciones.
        $supplyRequests = \Modules\INFRASTOCK\Entities\Request::with([
            'items.equipment.category',
            'productiveUnitWarehouse.productiveUnit',
            'productiveUnitWarehouse.warehouse',
            'user' => function($query) {
                $query->with('roles');
            }
        ])
        ->whereIn('status', ['pending', 'approved', 'rejected'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Cargar notificaciones para el usuario actual
        $notifications = \Modules\INFRASTOCK\Entities\Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', auth()->id())
            ->whereIn('type', ['request_created', 'request_approved', 'request_rejected'])
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        $notificationCount = $notifications->where('read_at', null)->count();

        // Datos adicionales para los selectores en los modales (si se usaran para crear/editar en el mismo modal).
        $equipments = Equipment::all();
        $users = User::all();
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get();

        // Obtener períodos disponibles para exportación
        $availablePeriods = $this->getAvailablePeriods();

        // Retorna la vista index de solicitudes con todos los datos necesarios.
        return view('infrastock::admin.supply-requests.index', compact('supplyRequests', 'equipments', 'users', 'productiveUnitWarehouses', 'notifications', 'notificationCount', 'availablePeriods'));
    }

    /**
     * Muestra el formulario para crear una nueva solicitud de insumo.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.supply-requests.index');
    }

    /**
     * Almacena una nueva solicitud de insumo en la base de datos.
     * Realiza validación de los datos antes del almacenamiento.
     * @param Request $request La solicitud HTTP que contiene los datos de la solicitud.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'movement_id' => 'required|exists:equipments,id', // ID del insumo es obligatorio y debe existir.
            'user_id' => 'required|exists:users,id', // ID del usuario es obligatorio y debe existir.
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id', // ID de unidad/almacén es obligatorio y debe existir.
            'amount' => 'required|integer|min:1', // Cantidad solicitada es obligatoria, entera y mínimo 1.
        ]);

        // Crea un nuevo registro de movimiento de almacén con el rol 'Solicitud'.
        WarehouseMovement::create([
            'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
            'movement_id' => $request->movement_id,
            'item_type' => 'equipment',
            'user_id' => $request->user_id,
            'role' => 'Solicitud', // Estado inicial de la solicitud.
            'amount' => $request->amount,
        ]);

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supply-requests.index')->with('success', 'Solicitud de insumo creada exitosamente.');
    }

    /**
     * Muestra los detalles de una solicitud de insumo específica.
     * Redirige al index ya que la visualización se realiza a través del modal de edición en la vista principal.
     * @param int $id El ID de la solicitud de insumo.
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->route('infrastock.admin.supply-requests.index');
    }

    /**
     * Muestra el formulario para editar una solicitud de insumo específica.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID de la solicitud de insumo.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.supply-requests.index');
    }

    /**
     * Actualiza el estado de una solicitud de insumo existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados de la solicitud.
     * @param int $id El ID de la solicitud de insumo a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $supplyRequest = \Modules\INFRASTOCK\Entities\Request::findOrFail($id);
        
        // Obtener el ID del usuario autenticado de forma segura
        $userId = auth()->check() ? auth()->id() : null;
        
        // Si no hay usuario autenticado, usar el primer usuario admin como fallback
        if (!$userId) {
            $adminUser = \App\Models\User::first();
            $userId = $adminUser ? $adminUser->id : null;
        }

        // Actualiza la solicitud con el nuevo estado
        $supplyRequest->update([
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => $request->status === 'approved' ? now() : null,
            'rejected_at' => $request->status === 'rejected' ? now() : null,
            'approved_by' => $request->status === 'approved' ? $userId : null,
        ]);

        // Actualizar el estado de todos los items
        $supplyRequest->items()->update(['status' => $request->status]);

        // Si se aprueba la solicitud, crear registros en WarehouseMovement para cada item
        if ($request->status === 'approved') {
            // Cargar los items con sus relaciones
            $supplyRequest->load('items.equipment');
            
            foreach ($supplyRequest->items as $item) {
                // Verificar que el equipo existe y tiene stock suficiente
                $equipment = $item->equipment;
                if ($equipment && $equipment->hasStockFor($item->requested_amount)) {
                    WarehouseMovement::create([
                        'productive_unit_warehouse_id' => $supplyRequest->productive_unit_warehouse_id,
                        'movement_id' => null, // No hay un Movement tradicional para solicitudes de insumos
                        'equipment_id' => $item->equipment_id,
                        'item_type' => 'equipment',
                        'user_id' => $supplyRequest->user_id,
                        'role' => 'Entrega', // Indica que se está entregando/consumiendo el insumo
                        'amount' => $item->requested_amount,
                    ]);

                    // Crear registro de sobrante para cada item aprobado
                    // Inicialmente la cantidad sobrante es 0, el usuario la actualizará después
                    \Modules\INFRASTOCK\Entities\Surplus::create([
                        'equipment_id' => $item->equipment_id,
                        'user_id' => $supplyRequest->user_id,
                        'request_id' => $supplyRequest->id,
                        'request_item_id' => $item->id,
                        'surplus_amount' => 0, // Inicialmente 0, se actualizará cuando el usuario registre el sobrante
                        'reason' => 'Sobrante de solicitud aprobada',
                        'description' => null, // Se completará cuando el usuario edite el sobrante
                        'surplus_date' => now(),
                        'status' => 'pending',
                    ]);
                }
            }
        }

        // Enviar notificación al personal de aseo
        $this->notifyCleaningStaffRequestStatus($supplyRequest, $request->status);

        // Si es una petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Estado de la solicitud actualizado exitosamente.',
                'status' => $request->status
            ]);
        }

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supply-requests.index')->with('success', 'Estado de la solicitud actualizado exitosamente.');
    }

    /**
     * Elimina una solicitud de insumo de la base de datos.
     * @param int $id El ID de la solicitud de insumo a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $supplyRequest = \Modules\INFRASTOCK\Entities\Request::findOrFail($id);
        $supplyRequest->delete();

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supply-requests.index')->with('success', 'Solicitud eliminada exitosamente.');
    }

    /**
     * Enviar notificación al usuario cuando se aprueba/rechaza una solicitud
     */
    private function notifyCleaningStaffRequestStatus($requestData, $status)
    {
        try {
            // Cargar el usuario con sus roles para determinar la ruta correcta
            $requestData->load('user.roles');
            
            $totalItems = $requestData->items->count();
            $equipmentNames = $requestData->items->pluck('equipment.name')->toArray();
            $equipmentList = implode(', ', array_slice($equipmentNames, 0, 3));
            if (count($equipmentNames) > 3) {
                $equipmentList .= ' y ' . (count($equipmentNames) - 3) . ' más';
            }

            // Determinar la ruta correcta según el rol del usuario
            $userRoles = $requestData->user->roles->pluck('name')->toArray();
            $actionUrl = route('infrastock.operator.requests.index'); // Por defecto
            
            if (in_array('Aseo', $userRoles)) {
                $actionUrl = route('infrastock.cleaning-staff.requests.index');
            } elseif (in_array('Operario', $userRoles)) {
                $actionUrl = route('infrastock.operator.requests.index');
            } elseif (in_array('Centro de Convivencia', $userRoles)) {
                $actionUrl = route('infrastock.convivencia.requests.index');
            } elseif (in_array('Ganadería', $userRoles)) {
                $actionUrl = route('infrastock.ganaderia.requests.index');
            }

            if ($status === 'approved') {
                \Modules\INFRASTOCK\Entities\Notification::create([
                    'type' => 'request_approved',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $requestData->user_id,
                    'data' => [
                        'title' => 'Solicitud Aprobada',
                        'message' => "Tu solicitud con {$totalItems} insumos ha sido aprobada: {$equipmentList}.",
                        'request_id' => $requestData->id,
                        'total_items' => $totalItems,
                        'equipment_list' => $equipmentList,
                        'action_url' => $actionUrl,
                        'created_at' => now()->format('d/m/Y H:i'),
                    ],
                ]);
            } elseif ($status === 'rejected') {
                \Modules\INFRASTOCK\Entities\Notification::create([
                    'type' => 'request_rejected',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $requestData->user_id,
                    'data' => [
                        'title' => 'Solicitud Rechazada',
                        'message' => "Tu solicitud con {$totalItems} insumos ha sido rechazada: {$equipmentList}.",
                        'request_id' => $requestData->id,
                        'total_items' => $totalItems,
                        'equipment_list' => $equipmentList,
                        'rejection_reason' => $requestData->rejection_reason,
                        'action_url' => $actionUrl,
                        'created_at' => now()->format('d/m/Y H:i'),
                    ],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación al usuario: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los períodos disponibles que tienen registros de consumo
     */
    private function getAvailablePeriods()
    {
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Obtener todos los consumos agrupados por año y mes
        $consumptions = WarehouseMovement::where('role', 'Entrega')
            ->where('item_type', 'equipment')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        if ($consumptions->isEmpty()) {
            return [
                'hasRecords' => false,
                'years' => []
            ];
        }

        // Organizar por años
        $years = [];
        foreach ($consumptions as $record) {
            $year = $record->year;
            $month = $record->month;
            
            if (!isset($years[$year])) {
                $years[$year] = [
                    'year' => $year,
                    'months' => [],
                    'quarters' => [],
                    'totalRecords' => 0
                ];
            }
            
            $years[$year]['months'][$month] = [
                'month' => $month,
                'name' => $monthNames[$month],
                'count' => $record->count
            ];
            $years[$year]['totalRecords'] += $record->count;
            
            // Calcular trimestre
            $quarter = ceil($month / 3);
            if (!isset($years[$year]['quarters'][$quarter])) {
                $years[$year]['quarters'][$quarter] = [
                    'quarter' => $quarter,
                    'name' => 'Q' . $quarter,
                    'months' => [],
                    'count' => 0
                ];
            }
            $years[$year]['quarters'][$quarter]['months'][] = $month;
            $years[$year]['quarters'][$quarter]['count'] += $record->count;
        }

        // Ordenar meses y trimestres
        foreach ($years as &$yearData) {
            ksort($yearData['months']);
            ksort($yearData['quarters']);
        }

        return [
            'hasRecords' => true,
            'years' => $years
        ];
    }

    /**
     * Obtiene las fechas de inicio y fin según los parámetros
     */
    private function getPeriodDates($type, $year = null, $month = null, $quarter = null)
    {
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $year = $year ?? Carbon::now()->year;
        
        switch ($type) {
            case 'monthly':
                $month = $month ?? Carbon::now()->month;
                $date = Carbon::create($year, $month, 1);
                return [
                    'start' => $date->copy()->startOfMonth(),
                    'end' => $date->copy()->endOfMonth(),
                    'label' => $monthNames[$month] . ' ' . $year
                ];
            case 'quarterly':
                $quarter = $quarter ?? Carbon::now()->quarter;
                $startMonth = (($quarter - 1) * 3) + 1;
                $date = Carbon::create($year, $startMonth, 1);
                return [
                    'start' => $date->copy()->startOfQuarter(),
                    'end' => $date->copy()->endOfQuarter(),
                    'label' => 'Q' . $quarter . ' ' . $year . ' (Trimestre ' . $quarter . ')'
                ];
            case 'yearly':
                $date = Carbon::create($year, 1, 1);
                return [
                    'start' => $date->copy()->startOfYear(),
                    'end' => $date->copy()->endOfYear(),
                    'label' => 'Año ' . $year
                ];
            default:
                $month = $month ?? Carbon::now()->month;
                $date = Carbon::create($year, $month, 1);
                return [
                    'start' => $date->copy()->startOfMonth(),
                    'end' => $date->copy()->endOfMonth(),
                    'label' => $monthNames[$month] . ' ' . $year
                ];
        }
    }

    /**
     * Obtiene los datos de consumos para exportación
     */
    private function getConsumptionData($type, $year = null, $month = null, $quarter = null)
    {
        $periodDates = $this->getPeriodDates($type, $year, $month, $quarter);
        
        // Obtener consumos (entregas de insumos)
        $consumptions = WarehouseMovement::with([
                'equipment.category',
                'user.roles',
                'productiveUnitWarehouse.productiveUnit'
            ])
            ->where('role', 'Entrega')
            ->where('item_type', 'equipment')
            ->whereBetween('created_at', [$periodDates['start'], $periodDates['end']])
            ->orderBy('created_at', 'desc')
            ->get();

        // Top 5 insumos más consumidos
        $topConsumed = WarehouseMovement::with('equipment.category')
            ->select('equipment_id')
            ->selectRaw('SUM(amount) as total_consumed')
            ->where('role', 'Entrega')
            ->where('item_type', 'equipment')
            ->whereBetween('created_at', [$periodDates['start'], $periodDates['end']])
            ->groupBy('equipment_id')
            ->orderByDesc('total_consumed')
            ->limit(5)
            ->get();

        // Estadísticas
        $stats = [
            'total_requests' => $consumptions->count(),
            'total_units' => $consumptions->sum('amount'),
            'unique_supplies' => $consumptions->pluck('equipment_id')->unique()->count(),
        ];

        return [
            'consumptions' => $consumptions,
            'topConsumed' => $topConsumed,
            'periodLabel' => $periodDates['label'],
            'stats' => $stats,
        ];
    }

    /**
     * Exporta los consumos de insumos a Excel
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $year = $request->get('year');
        $month = $request->get('month');
        $quarter = $request->get('quarter');
        
        $data = $this->getConsumptionData($type, $year, $month, $quarter);
        
        $filename = 'consumos_insumos_';
        if ($type === 'monthly' && $month && $year) {
            $filename .= $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
        } elseif ($type === 'quarterly' && $quarter && $year) {
            $filename .= $year . '_Q' . $quarter;
        } elseif ($type === 'yearly' && $year) {
            $filename .= $year;
        } else {
            $filename .= now()->format('Y-m-d');
        }
        
        return Excel::download(
            new SupplyConsumptionExport(
                $data['consumptions'],
                $data['topConsumed'],
                $type,
                $data['periodLabel'],
                $data['stats']
            ),
            $filename . '.xlsx'
        );
    }

    /**
     * Exporta los consumos de insumos a PDF
     */
    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $year = $request->get('year');
        $month = $request->get('month');
        $quarter = $request->get('quarter');
        
        $data = $this->getConsumptionData($type, $year, $month, $quarter);
        $adminName = auth()->user()->name ?? 'Administrador del Sistema';
        
        // Convertir logo a base64
        $logoPath = public_path('assets/img/logo.png');
        $base64Logo = '';
        if (file_exists($logoPath)) {
            $type_img = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = file_get_contents($logoPath);
            $base64Logo = 'data:image/' . $type_img . ';base64,' . base64_encode($logoData);
        }

        $pdf = PDF::loadView('infrastock::admin.supply-requests.exports.pdf', [
            'consumptions' => $data['consumptions'],
            'topConsumed' => $data['topConsumed'],
            'period' => $type,
            'periodLabel' => $data['periodLabel'],
            'stats' => $data['stats'],
            'adminName' => $adminName,
            'base64Logo' => $base64Logo,
        ]);

        $pdf->setPaper('letter', 'landscape');
        
        $filename = 'consumos_insumos_';
        if ($type === 'monthly' && $month && $year) {
            $filename .= $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
        } elseif ($type === 'quarterly' && $quarter && $year) {
            $filename .= $year . '_Q' . $quarter;
        } elseif ($type === 'yearly' && $year) {
            $filename .= $year;
        } else {
            $filename .= now()->format('Y-m-d');
        }
        
        return $pdf->stream($filename . '.pdf');
    }
}
