<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateInfrastockRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // IDs de los roles existentes que se deben mantener
        $keepRoleIds = [39, 40, 43, 44]; // Operario, Aseo, Ganadería, Centro de Convivencia
        
        // Buscar roles duplicados de INFRASTOCK que no sean los que queremos mantener
        // Eliminar roles con los mismos nombres pero diferentes IDs
        $roleNames = ['Operario', 'Aseo', 'Ganadería', 'Centro de Convivencia'];
        
        foreach ($roleNames as $roleName) {
            // Obtener todos los roles con este nombre
            $roles = DB::table('roles')
                ->where('name', $roleName)
                ->whereNotIn('id', $keepRoleIds)
                ->get();
            
            // Eliminar los roles duplicados (mantener solo los que están en keepRoleIds)
            foreach ($roles as $role) {
                // Verificar si hay usuarios asignados a este rol
                $userCount = DB::table('role_user')->where('role_id', $role->id)->count();
                
                if ($userCount == 0) {
                    // Si no hay usuarios, eliminar el rol
                    DB::table('roles')->where('id', $role->id)->delete();
                    \Log::info("Rol duplicado eliminado: {$roleName} (ID: {$role->id})");
                } else {
                    // Si hay usuarios, solo loguear la advertencia
                    \Log::warning("No se puede eliminar el rol {$roleName} (ID: {$role->id}) porque tiene {$userCount} usuarios asignados");
                }
            }
        }
        
        \Log::info('Migración: Roles duplicados de INFRASTOCK eliminados. Se mantienen los roles con IDs: ' . implode(', ', $keepRoleIds));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No hay forma de revertir esto de manera segura
        \Log::info('Migración revertida: No se pueden restaurar roles eliminados automáticamente');
    }
}

