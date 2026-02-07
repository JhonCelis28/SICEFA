<?php

namespace Modules\INFRASTOCK\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\INFRASTOCK\Entities\Notification;

class ShareNotifications
{
    /**
     * Handle an incoming request.
     * Comparte las notificaciones del usuario actual con todas las vistas,
     * usando el sistema de prioridades para mostrar las más relevantes primero.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // Usar el método que respeta prioridades y retención
            $notifications = Notification::getActiveForUser(auth()->id());
            $notificationCount = $notifications->whereNull('read_at')->count();

            // Compartir con todas las vistas
            view()->share([
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
            ]);
        }

        return $next($request);
    }
}
