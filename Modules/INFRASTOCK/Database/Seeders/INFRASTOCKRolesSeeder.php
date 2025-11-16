<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SICA\Entities\App;
use Modules\SICA\Entities\Role;

class INFRASTOCKRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Consultar aplicación INFRASTOCK para registrar los roles
        $app = App::where('name', 'INFRASTOCK')->first();

        if (!$app) {
            $this->command->error('No se encontró la aplicación INFRASTOCK. Asegúrate de ejecutar AppTableSeeder primero.');
            return;
        }

        // Registrar o actualizar rol de ADMINISTRADOR
        $role_admin = Role::updateOrCreate(['slug' => 'infrastock.admin'], [
            'name' => 'Administrador',
            'description' => 'Rol Administrador del módulo INFRASTOCK',
            'description_english' => 'INFRASTOCK Module Administrator Role',
            'full_access' => 'Si',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de OPERARIO
        $role_operator = Role::updateOrCreate(['slug' => 'operario'], [
            'name' => 'Operario',
            'description' => 'Personal operativo del módulo INFRASTOCK',
            'description_english' => 'Operative staff of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de ASEO
        $role_cleaning = Role::updateOrCreate(['slug' => 'aseo'], [
            'name' => 'Aseo',
            'description' => 'Personal de aseo del módulo INFRASTOCK',
            'description_english' => 'Cleaning staff of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de GANADERÍA
        $role_livestock = Role::updateOrCreate(['slug' => 'infrastock.ganaderia'], [
            'name' => 'Ganadería',
            'description' => 'Rol de Ganadería del módulo INFRASTOCK',
            'description_english' => 'Livestock role of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de CENTRO DE CONVIVENCIA
        $role_convivencia = Role::updateOrCreate(['slug' => 'infrastock.centro-convivencia'], [
            'name' => 'Centro de Convivencia',
            'description' => 'Rol de Centro de Convivencia del módulo INFRASTOCK',
            'description_english' => 'Coexistence Center role of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de VIGILANCIA
        $role_security = Role::updateOrCreate(['slug' => 'infrastock.vigilancia'], [
            'name' => 'Vigilancia',
            'description' => 'Rol de Vigilancia del módulo INFRASTOCK',
            'description_english' => 'Security role of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de AGROINDUSTRIA
        $role_agroindustry = Role::updateOrCreate(['slug' => 'infrastock.agroindustria'], [
            'name' => 'Agroindustria',
            'description' => 'Rol de Agroindustria del módulo INFRASTOCK',
            'description_english' => 'Agroindustry role of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de CIENCIAS BÁSICAS
        $role_basic_sciences = Role::updateOrCreate(['slug' => 'infrastock.ciencias-basicas'], [
            'name' => 'Ciencias Basicas',
            'description' => 'Rol de Ciencias Básicas del módulo INFRASTOCK',
            'description_english' => 'Basic Sciences role of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        // Registrar o actualizar rol de PSICOLA
        $role_psicola = Role::updateOrCreate(['slug' => 'infrastock.psicola'], [
            'name' => 'Psicola',
            'description' => 'Rol de Psicola del módulo INFRASTOCK',
            'description_english' => 'Psicola role of INFRASTOCK module',
            'full_access' => 'No',
            'app_id' => $app->id
        ]);

        $this->command->info('Roles de INFRASTOCK registrados/actualizados correctamente.');
    }
}
