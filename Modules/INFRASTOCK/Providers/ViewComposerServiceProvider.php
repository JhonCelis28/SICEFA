<?php

namespace Modules\INFRASTOCK\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\Notification;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Carbon\Carbon;

/**
 * @class ViewComposerServiceProvider
 * @brief Proveedor de servicios para componer vistas en el módulo INFRASTOCK.
 *
 * Comparte datos dinámicos (conteos de solicitudes, notificaciones con prioridad)
 * con las vistas del módulo para que el navbar y dashboard muestren información actualizada.
 */
class ViewComposerServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // View composer para el layout admin
        View::composer('infrastock::layouts.master', function ($view) {
            // Conteo de solicitudes de insumos pendientes
            $pendingSupplyRequestsCount = WarehouseMovement::where('item_type', 'supply')
                                            ->where('role', 'Solicitud')
                                            ->count();

            // Conteo de insumos próximos a vencer
            $expiringSuppliesCount = Equipment::whereNotNull('expiration_date')
                                                ->where('expiration_date', '<=', Carbon::now()->addDays(30))
                                                ->count();

            // Cargar notificaciones con sistema de prioridades
            $notifications = collect();
            $notificationCount = 0;

            if (auth()->check()) {
                // Usar el método getActiveForUser que respeta retención por prioridad
                // y ordena: no leídas primero, luego por prioridad (crítica > alta > media > baja)
                $notifications = Notification::getActiveForUser(auth()->id());
                $notificationCount = $notifications->whereNull('read_at')->count();
            }

            $view->with(compact(
                'pendingSupplyRequestsCount',
                'expiringSuppliesCount',
                'notifications',
                'notificationCount'
            ));
        });
    }

    public function provides()
    {
        return [];
    }
}
