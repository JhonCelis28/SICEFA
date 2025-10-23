<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Notification;

class NotificationController extends Controller
{
    /**
     * Marcar una notificación como leída
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

            $notification->markAsRead();

            return response()->json(['success' => true, 'message' => 'Notificación marcada como leída']);
        } catch (\Exception $e) {
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
