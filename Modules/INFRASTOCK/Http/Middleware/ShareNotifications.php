<?php

namespace Modules\INFRASTOCK\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Notification;
use Carbon\Carbon;

class ShareNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Obtener notificaciones recientes del usuario desde la tabla notifications
            $notifications = Notification::where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', $user->id)
                ->whereIn('type', ['request_created', 'request_approved', 'request_rejected'])
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get();

            $notificationCount = $notifications->where('read_at', null)->count();

            // Compartir las variables con todas las vistas
            view()->share([
                'notifications' => $notifications,
                'notificationCount' => $notificationCount
            ]);
        }

        return $next($request);
    }
}
