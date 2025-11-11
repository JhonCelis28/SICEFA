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
        // DESACTIVADO: Los roles ya existen en el sistema con IDs: 39 (Operario), 40 (Aseo), 43 (Ganadería), 44 (Centro de Convivencia)
        // No crear roles duplicados. Usar los roles existentes.
        
        $this->command->info('Seeder desactivado. Los roles de INFRASTOCK ya existen en el sistema.');
        $this->command->info('Roles existentes: ID 39 (Operario), ID 40 (Aseo), ID 43 (Ganadería), ID 44 (Centro de Convivencia)');
        
        /*
        // Código original comentado para referencia
        $roles = [
            [
                'name' => 'Operario',
                'slug' => 'operario',
                'description' => 'Personal operativo del módulo INFRASTOCK',
                'app_id' => 19,
            ],
            [
                'name' => 'Aseo',
                'slug' => 'aseo',
                'description' => 'Personal de aseo del módulo INFRASTOCK',
                'app_id' => 19,
            ],
            [
                'name' => 'Centro de Convivencia',
                'slug' => 'centro-convivencia',
                'description' => 'Personal del Centro de Convivencia del módulo INFRASTOCK',
                'app_id' => 19,
            ],
            [
                'name' => 'Ganadería',
                'slug' => 'ganaderia',
                'description' => 'Personal de Ganadería del módulo INFRASTOCK',
                'app_id' => 19,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
        */
    }
}
