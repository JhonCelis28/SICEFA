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
            
            // Determinar el rol del usuario para redirecciones correctas
            $user = auth()->user();
            $userRoles = $user->roles->pluck('name')->toArray();
            $isAdmin = in_array('Administrador', $userRoles) || in_array('Super Administrador', $userRoles);
            $isInstructor = in_array('Instructor', $userRoles);
            
            // Mapeo de roles no-admin a sus prefijos de ruta
            $roleRoutePrefixes = [
                'Psicola' => 'psicola',
                'Aseo' => 'cleaning-staff',
                'Personal de Aseo' => 'cleaning-staff',
                'Operario' => 'operator',
                'Centro de Convivencia' => 'convivencia',
                'Ganadería' => 'ganaderia',
                'Vigilancia' => 'vigilancia',
                'Ciencias Basicas' => 'ciencias-basicas',
                'Agroindustria' => 'agroindustria',
            ];
            
            $userRoutePrefix = null;
            foreach ($roleRoutePrefixes as $roleName => $prefix) {
                if (in_array($roleName, $userRoles)) {
                    $userRoutePrefix = $prefix;
                    break;
                }
            }
            
            // Si es una notificación de insumo próximo a vencer, redirigir a la página de insumos con filtro
            if ($type === 'supply_expiring' && $equipmentId) {
                $response['equipment_id'] = $equipmentId;
                if ($isAdmin) {
                    $response['redirect_url'] = route('infrastock.admin.supplies.index', ['filter_equipment_id' => $equipmentId]);
                }
            }
            // Si es una notificación de solicitud creada, redirigir a la página de solicitudes
            elseif ($type === 'request_created') {
                if ($isAdmin) {
                    $response['redirect_url'] = route('infrastock.admin.supply-requests.index');
                } elseif ($userRoutePrefix) {
                    $response['redirect_url'] = route("infrastock.{$userRoutePrefix}.requests.index");
                }
            }
            // Si es una notificación de solicitud aprobada/rechazada, redirigir al usuario a sus solicitudes
            elseif (in_array($type, ['request_approved', 'request_rejected'])) {
                if ($isAdmin) {
                    $response['redirect_url'] = route('infrastock.admin.supply-requests.index');
                } elseif ($userRoutePrefix) {
                    $response['redirect_url'] = route("infrastock.{$userRoutePrefix}.requests.index");
                }
            }
            // Si es una notificación de sobrante/devolución, redirigir a la página de devoluciones
            elseif ($type === 'surplus_reported') {
                if ($isAdmin) {
                    $response['redirect_url'] = route('infrastock.admin.supply-returns.index');
                }
            }
            // Si es una notificación de préstamo de herramienta creado
            elseif ($type === 'loan_created') {
                if ($isAdmin) {
                    $response['redirect_url'] = route('infrastock.admin.loans.index');
                } elseif ($isInstructor) {
                    $response['redirect_url'] = route('infrastock.instructor.my-loans');
                } elseif (isset($notification->data['action_url'])) {
                    $response['redirect_url'] = $notification->data['action_url'];
                }
            }
            // Si es una notificación de préstamo aprobado o rechazado
            elseif (in_array($type, ['loan_approved', 'loan_rejected'])) {
                if ($isInstructor) {
                    $response['redirect_url'] = route('infrastock.instructor.my-loans');
                } elseif (isset($notification->data['action_url'])) {
                    $response['redirect_url'] = $notification->data['action_url'];
                }
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
