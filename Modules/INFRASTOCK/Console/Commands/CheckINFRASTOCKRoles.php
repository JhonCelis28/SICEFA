<?php

namespace Modules\INFRASTOCK\Console\Commands;

use Illuminate\Console\Command;
use Modules\SICA\Entities\Role;

class CheckINFRASTOCKRoles extends Command
{
    protected $signature = 'infrastock:check-roles';
    protected $description = 'Verificar que los roles de INFRASTOCK existen';

    public function handle()
    {
        $this->info('Verificando roles de INFRASTOCK...');
        
        $roles = ['Operario', 'Aseo'];
        
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $this->info("✅ {$role->name} (ID: {$role->id})");
            } else {
                $this->error("❌ {$roleName} no encontrado");
            }
        }
        
        $this->info('Verificación completada.');
    }
}
