<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\SICA\Entities\App;

/**
 * @class AppTableSeeder
 * @brief Seeder para la tabla `apps` del módulo INFRASTOCK.
 *
 * Este seeder se encarga de registrar o actualizar la información de la aplicación
 * INFRASTOCK en la tabla `apps`, asegurando que el módulo sea visible y accesible
 * dentro del sistema principal. Proporciona detalles como el nombre, URL, color,
 * ícono y descripción de la aplicación.
 */
class AppTableSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     *
     * Este método se encarga de crear o actualizar el registro de la aplicación
     * INFRASTOCK en la tabla `apps`. Si la aplicación con el nombre 'INFRASTOCK'
     * ya existe, sus datos serán actualizados; de lo contrario, se creará un nuevo registro.
     *
     * @return void
     */
    public function run(): void
    {

        /* Registro o actualización de la nueva aplicación Sistema Integrado de Control Administrativo */
        App::updateOrCreate(
    ['name' => 'INFRASTOCK'], // criterio de búsqueda
    [
        'name' => 'INFRASTOCK', // 👈 esto lo fuerza a mayúsculas
        'url' => '/infrastock',
        'color' => '#1a8f03ff',
        'icon' => 'fa fa-tools',
        'description' => 'Sistema de Registro y control de Herramientas e Insumos',
        'description_english' => 'Integrated Administrative Control System'
    ]
);
    }
}