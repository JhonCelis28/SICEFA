<?php

namespace Modules\INFRASTOCK\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * @class RouteServiceProvider
 * @brief Proveedor de servicios para las rutas del módulo INFRASTOCK.
 *
 * Este ServiceProvider es responsable de definir y cargar las rutas web y API
 * específicas para el módulo INFRASTOCK. Asegura que las rutas del módulo
 * se carguen correctamente dentro del contexto de la aplicación Laravel.
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleNamespace El espacio de nombres del controlador del módulo
     *                               para asumir al generar URLs a acciones.
     */
    protected $moduleNamespace = 'Modules\INFRASTOCK\Http\Controllers';

    /**
     * Llamado antes de que las rutas sean registradas.
     * Registra cualquier enlace de modelo o filtros basados en patrones.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot(); // Llama al método boot de la clase padre.
    }

    /**
     * Define las rutas para el módulo INFRASTOCK.
     * Este método es el punto de entrada para cargar tanto las rutas web como las API.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes(); // Carga las rutas de la API del módulo.

        $this->mapWebRoutes(); // Carga las rutas web del módulo.
    }

    /**
     * Define las rutas "web" para el módulo INFRASTOCK.
     * Estas rutas reciben estado de sesión, protección CSRF, etc.
     * Se agrupan bajo el middleware 'web' y usan el espacio de nombres del controlador del módulo.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(__DIR__.'/../Routes/web.php'); // Carga el archivo de rutas web del módulo.
    }

    /**
     * Define las rutas "api" para el módulo INFRASTOCK.
     * Estas rutas son típicamente sin estado.
     * Se prefijan con 'api', se agrupan bajo el middleware 'api' y usan el espacio de nombres del controlador del módulo.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('INFRASTOCK', '/Routes/api.php')); // Carga el archivo de rutas API del módulo.
    }
}
