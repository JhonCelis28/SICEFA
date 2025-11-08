<?php

namespace Modules\INFRASTOCK\Database\Seeders;

use Illuminate\Database\Seeder;
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
        // Crear roles específicos para INFRASTOCK (app_id = 23)
        $roles = [
            [
                'name' => 'Operario',
                'slug' => 'operario',
                'description' => 'Personal operativo del módulo INFRASTOCK',
                'app_id' => 23,
            ],
            [
                'name' => 'Aseo',
                'slug' => 'aseo',
                'description' => 'Personal de aseo del módulo INFRASTOCK',
                'app_id' => 23,
            ],
            [
                'name' => 'Centro de Convivencia',
                'slug' => 'centro-convivencia',
                'description' => 'Personal del Centro de Convivencia del módulo INFRASTOCK',
                'app_id' => 23,
            ],
            [
                'name' => 'Ganadería',
                'slug' => 'ganaderia',
                'description' => 'Personal de Ganadería del módulo INFRASTOCK',
                'app_id' => 23,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $this->command->info('Roles de INFRASTOCK creados exitosamente.');
    }
}
