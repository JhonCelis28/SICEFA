<?php

namespace Modules\INFRASTOCK\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateINFRASTOCKRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infrastock:create-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create roles for INFRASTOCK module';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creating roles for INFRASTOCK module...');

        try {
            // Primero obtener o crear la app INFRASTOCK
            $appId = DB::table('apps')->where('name', 'INFRASTOCK')->value('id');
            
            if (!$appId) {
                $this->info('Creating INFRASTOCK app...');
                $appId = DB::table('apps')->insertGetId([
                    'name' => 'INFRASTOCK',
                    'url' => '/infrastock',
                    'color' => '#1a8f03ff',
                    'icon' => 'fa fa-tools',
                    'description' => 'Sistema de Registro y control de Herramientas e Insumos',
                    'description_english' => 'Integrated Administrative Control System',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("INFRASTOCK app created with ID: {$appId}");
            } else {
                $this->info("INFRASTOCK app found with ID: {$appId}");
            }

            // Crear roles
            $roles = [
                [
                    'name' => 'Operario',
                    'slug' => 'operario',
                    'description' => 'Personal operativo del módulo INFRASTOCK',
                    'app_id' => $appId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Aseo',
                    'slug' => 'aseo',
                    'description' => 'Personal de aseo del módulo INFRASTOCK',
                    'app_id' => $appId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($roles as $roleData) {
                $existingRole = DB::table('roles')->where('slug', $roleData['slug'])->first();
                
                if (!$existingRole) {
                    DB::table('roles')->insert($roleData);
                    $this->info("Role '{$roleData['name']}' created successfully");
                } else {
                    $this->info("Role '{$roleData['name']}' already exists");
                }
            }

            $this->info('Roles creation completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error creating roles: ' . $e->getMessage());
            return 1;
        }
    }
}

