<?php

namespace Modules\INFRASTOCK\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Carbon\Carbon;

/**
 * @class ViewComposerServiceProvider
 * @brief Proveedor de servicios para componer vistas en el módulo INFRASTOCK.
 *
 * Este ServiceProvider se encarga de compartir datos específicos (como conteos
 * de solicitudes pendientes o insumos próximos a vencer) con las vistas del módulo,
 * particularmente con el layout principal (`infrastock::layouts.master`).
 * Esto permite que la barra de navegación u otros elementos visuales muestren
 * información dinámica y actualizada en tiempo real.
 */
class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Registra el proveedor de servicios.
     * Este método se utiliza para enlazar o registrar servicios en el contenedor de la aplicación.
     * Actualmente, no se registran servicios directos aquí, pero se mantiene para futura expansión.
     *
     * @return void
     */
    public function register()
    {
        // No hay servicios directos para registrar en este momento.
    }

    /**
     * Arranca los eventos de la aplicación.
     * Este método se ejecuta después de que todos los demás proveedores de servicios
     * hayan sido registrados. Aquí se definen los "view composers".
     *
     * @return void
     */
    public function boot()
    {
        // Define un view composer para la vista 'infrastock::layouts.master'.
        // Esto significa que la función callback se ejecutará cada vez que se cargue esa vista,
        // y los datos resultantes estarán disponibles en la vista.
        View::composer('infrastock::layouts.master', function ($view) {
            // Calcular el conteo de solicitudes de insumos pendientes.
            // Se busca en el modelo WarehouseMovement aquellos registros que son de tipo 'supply'
            // y tienen el rol 'Solicitud', indicando que aún no han sido gestionados.
            $pendingSupplyRequestsCount = WarehouseMovement::where('item_type', 'supply')
                                            ->where('role', 'Solicitud') // Filtrar por el rol que indica una solicitud pendiente.
                                            ->count();

            // Calcular el conteo de insumos próximos a vencer.
            // Se buscan en el modelo Equipment aquellos insumos que tienen una fecha de vencimiento
            // definida y que vencen en los próximos 30 días a partir de la fecha actual.
            $expiringSuppliesCount = Equipment::whereNotNull('expiration_date')
                                                ->where('expiration_date', '<=', Carbon::now()->addDays(30))
                                                ->count();

            // Comparte las variables calculadas con la vista.
            $view->with(compact('pendingSupplyRequestsCount', 'expiringSuppliesCount'));
        });
    }

    /**
     * Obtiene los servicios proporcionados por el proveedor.
     * Este método indica qué servicios son proporcionados por este ServiceProvider.
     * Actualmente, este proveedor no proporciona servicios directos, por lo que retorna un array vacío.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
