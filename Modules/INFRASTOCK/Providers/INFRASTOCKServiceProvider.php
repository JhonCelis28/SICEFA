<?php

namespace Modules\INFRASTOCK\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

/**
 * @class INFRASTOCKServiceProvider
 * @brief Proveedor de servicios principal para el módulo INFRASTOCK.
 *
 * Este ServiceProvider es responsable de registrar y arrancar todos los componentes
 * necesarios para el funcionamiento del módulo INFRASTOCK, incluyendo configuraciones,
 * vistas, traducciones, migraciones y otros proveedores de servicios específicos del módulo.
 */
class INFRASTOCKServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName El nombre en mayúsculas del módulo.
     */
    protected $moduleName = 'INFRASTOCK';

    /**
     * @var string $moduleNameLower El nombre en minúsculas del módulo.
     */
    protected $moduleNameLower = 'infrastock';

    /**
     * Arranca los eventos de la aplicación.
     * Este método se ejecuta después de que todos los demás proveedores de servicios
     * hayan sido registrados.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations(); // Registra las traducciones del módulo.
        $this->registerConfig();       // Registra los archivos de configuración del módulo.
        $this->registerViews();        // Registra las vistas del módulo.
        // Carga las migraciones de la base de datos del módulo.
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        
        // Registrar comandos de Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\INFRASTOCK\Console\Commands\CheckINFRASTOCKRoles::class,
                \Modules\INFRASTOCK\Console\Commands\CheckUserData::class,
                \Modules\INFRASTOCK\Console\Commands\CheckPersonData::class,
                \Modules\INFRASTOCK\Console\Commands\TestPhoneField::class,
                \Modules\INFRASTOCK\Console\Commands\CheckTableStructure::class,
                \Modules\INFRASTOCK\Console\Commands\CreateINFRASTOCKRoles::class,
            ]);
        }
    }

    /**
     * Registra el proveedor de servicios.
     * Este método se encarga de enlazar o registrar servicios dentro del contenedor de la aplicación.
     *
     * @return void
     */
    public function register()
    {
        // Registra el RouteServiceProvider para cargar las rutas del módulo.
        $this->app->register(RouteServiceProvider::class);
        // Registra el ViewComposerServiceProvider para compartir datos con las vistas del módulo.
        $this->app->register(ViewComposerServiceProvider::class);
    }

    /**
     * Registra los archivos de configuración del módulo.
     * Publica el archivo de configuración para que pueda ser modificado por el usuario
     * y fusiona la configuración por defecto del módulo con la configuración de la aplicación.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Registra las vistas del módulo.
     * Publica las vistas para que puedan ser sobrescritas si es necesario
     * y carga las vistas desde la ruta del módulo y las rutas publicables.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Registra las traducciones del módulo.
     * Carga los archivos de traducción desde la carpeta de recursos del módulo
     * o desde la ruta de recursos publicada si existe.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Obtiene los servicios proporcionados por el proveedor.
     * Indica qué servicios son proporcionados por este ServiceProvider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Obtiene las rutas publicables para las vistas del módulo.
     * Este método busca rutas de vistas publicadas en el directorio de recursos de la aplicación.
     *
     * @return array
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
