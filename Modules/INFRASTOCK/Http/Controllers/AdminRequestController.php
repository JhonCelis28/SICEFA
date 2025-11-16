<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\Notification;
use App\Models\User;

class AdminRequestController extends Controller
{
    /**
     * Muestra todas las solicitudes pendientes para el administrador
     */
    public function index()
    {
        $requests = \Modules\INFRASTOCK\Entities\Request::with([
            'items.equipment.category',
            'productiveUnitWarehouse.productiveUnit',
            'productiveUnitWarehouse.warehouse',
            'user' => function($query) {
                $query->with('roles');
            }
        ])
        ->where('status', 'pending')
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

        return view('infrastock::admin.requests.index', compact('requests', 'notifications', 'notificationCount'));
    }

    /**
     * Aprueba una solicitud
     */
    public function approve(Request $request, $id)
    {
        $requestData = \Modules\INFRASTOCK\Entities\Request::with(['items.equipment', 'user'])
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();
        
        if (!$requestData) {
            return redirect()->back()->with('error', 'Solicitud no encontrada o ya procesada.');
        }

        try {
            // Actualizar el estado de la solicitud
            $requestData->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // Actualizar el estado de todos los items
            $requestData->items()->update(['status' => 'approved']);

            // Crear registros en WarehouseMovement para cada item aprobado
            foreach ($requestData->items as $item) {
                // Verificar que el equipo existe y tiene stock suficiente
                $equipment = $item->equipment;
                if ($equipment && $equipment->hasStockFor($item->requested_amount)) {
                    WarehouseMovement::create([
                        'productive_unit_warehouse_id' => $requestData->productive_unit_warehouse_id,
                        'movement_id' => null, // No hay un Movement tradicional para solicitudes de insumos
                        'equipment_id' => $item->equipment_id,
                        'item_type' => 'equipment',
                        'user_id' => $requestData->user_id,
                        'role' => 'Entrega', // Indica que se está entregando/consumiendo el insumo
                        'amount' => $item->requested_amount,
                    ]);

                    // Crear registro de sobrante para cada item aprobado
                    // Inicialmente la cantidad sobrante es 0, el usuario la actualizará después
                    \Modules\INFRASTOCK\Entities\Surplus::create([
                        'equipment_id' => $item->equipment_id,
                        'user_id' => $requestData->user_id,
                        'request_id' => $requestData->id,
                        'request_item_id' => $item->id,
                        'surplus_amount' => 0, // Inicialmente 0, se actualizará cuando el usuario registre el sobrante
                        'reason' => 'Sobrante de solicitud aprobada',
                        'description' => null, // Se completará cuando el usuario edite el sobrante
                        'surplus_date' => now(),
                        'status' => 'pending',
                    ]);
                }
            }

            // Enviar notificación al personal de aseo
            $this->notifyCleaningStaffRequestStatus($requestData, 'approved');

            return redirect()->back()->with('success', 'Solicitud aprobada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechaza una solicitud
     */
    public function reject(Request $request, $id)
    {
        $requestData = \Modules\INFRASTOCK\Entities\Request::with(['items.equipment', 'user'])
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();
        
        if (!$requestData) {
            return redirect()->back()->with('error', 'Solicitud no encontrada o ya procesada.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            // Actualizar el estado de la solicitud
            $requestData->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Actualizar el estado de todos los items
            $requestData->items()->update(['status' => 'rejected']);

            // Enviar notificación al personal de aseo
            $this->notifyCleaningStaffRequestStatus($requestData, 'rejected');

            return redirect()->back()->with('success', 'Solicitud rechazada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
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
                Notification::create([
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
                Notification::create([
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
}
