<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * @class INFRASTOCKDatabaseSeeder
 * @brief Seeder principal para el módulo INFRASTOCK.
 *
 * Este seeder orquesta la ejecución de otros seeders específicos dentro del módulo INFRASTOCK.
 * Su propósito principal es asegurar que todas las tablas necesarias para el funcionamiento
 * del módulo sean pobladas con datos iniciales o de prueba de manera controlada y transaccional.
 */
class INFRASTOCKDatabaseSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos para el módulo INFRASTOCK.
     *
     * Este método gestiona el proceso de siembra de la base de datos para el módulo.
     * Utiliza transacciones para asegurar la integridad de los datos, ejecutando
     * el `AppTableSeeder` para registrar la aplicación INFRASTOCK en el sistema.
     *
     * @return void
     */
    public function run(): void
    {
        
        DB::beginTransaction();

        $this->call(AppTableSeeder::class); // Ejecutar Seeder de aplicación
        $this->call(INFRASTOCKRolesSeeder::class); // Ejecutar Seeder de roles para usuarios
        $this->call(INFRASTOCKCategoriesSeeder::class); // Ejecutar Seeder de categorías
        $this->call(INFRASTOCKSuppliesSeeder::class); // Ejecutar Seeder de insumos
        $this->call(INFRASTOCKToolsSeeder::class); // Ejecutar Seeder de herramientas

        DB::commit();
    }
}
