<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Notification;

class NotificationController extends Controller
{
    /**
     * Marcar una notificación como leída
     * Para notificaciones de insumos próximos a vencer, se eliminan en lugar de solo marcarlas como leídas
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', auth()->id())
                ->first();

            if (!$notification) {
                return response()->json(['success' => false, 'message' => 'Notificación no encontrada'], 404);
            }

            $type = $notification->type;
            $equipmentId = null;
            
            // Guardar equipment_id antes de marcar
            if ($type === 'supply_expiring' && isset($notification->data['equipment_id'])) {
                $equipmentId = $notification->data['equipment_id'];
            }
            
            // Marcar todas las notificaciones como leídas (incluyendo las de insumos próximos a vencer)
            // Las notificaciones de insumos próximos a vencer se recrearán después de 7 días si el insumo sigue próximo a vencer
            $notification->markAsRead();
            
            // Devolver información adicional para redirección
            $response = [
                'success' => true, 
                'message' => 'Notificación marcada como leída',
                'type' => $type,
            ];
            
            // Si es una notificación de insumo próximo a vencer, redirigir a la página de insumos con filtro
            if ($type === 'supply_expiring' && $equipmentId) {
                $response['equipment_id'] = $equipmentId;
                $response['redirect_url'] = route('infrastock.admin.supplies.index', ['filter_equipment_id' => $equipmentId]);
            }
            // Si es una notificación de solicitud creada, redirigir a la página de solicitudes de insumos
            elseif ($type === 'request_created') {
                $response['redirect_url'] = route('infrastock.admin.supply-requests.index');
            }
            // Si es una notificación de sobrante/devolución, redirigir a la página de devoluciones
            elseif ($type === 'surplus_reported') {
                $response['redirect_url'] = route('infrastock.admin.supply-returns.index');
            }
            // Si la notificación tiene action_url, usarlo para redirección (para otros tipos)
            elseif (isset($notification->data['action_url'])) {
                $response['redirect_url'] = $notification->data['action_url'];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error al marcar notificación como leída: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al marcar la notificación'], 500);
        }
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead(Request $request)
    {
        try {
            Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al marcar las notificaciones'], 500);
        }
    }

    /**
     * Obtener notificaciones no leídas
     */
    public function getUnread(Request $request)
    {
        try {
            $notifications = Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', auth()->id())
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json(['success' => true, 'notifications' => $notifications]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener notificaciones'], 500);
        }
    }
}
