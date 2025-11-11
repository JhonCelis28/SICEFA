<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMissingInfrastockRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Obtener el app_id de INFRASTOCK
        $appId = DB::table('apps')->where('name', 'INFRASTOCK')->value('id');
        
        if (!$appId) {
            \Log::warning('No se encontró la aplicación INFRASTOCK');
            return;
        }
        
        $roles = [
            [
                'name' => 'Ganadería',
                'slug' => 'infrastock.ganaderia',
                'description' => 'Rol de Ganadería del módulo INFRASTOCK',
                'description_english' => 'Livestock role of INFRASTOCK module',
                'full_access' => 'No',
                'app_id' => $appId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Centro de Convivencia',
                'slug' => 'infrastock.centro-convivencia',
                'description' => 'Rol de Centro de Convivencia del módulo INFRASTOCK',
                'description_english' => 'Coexistence Center role of INFRASTOCK module',
                'full_access' => 'No',
                'app_id' => $appId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vigilancia',
                'slug' => 'infrastock.vigilancia',
                'description' => 'Rol de Vigilancia del módulo INFRASTOCK',
                'description_english' => 'Security role of INFRASTOCK module',
                'full_access' => 'No',
                'app_id' => $appId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agroindustria',
                'slug' => 'infrastock.agroindustria',
                'description' => 'Rol de Agroindustria del módulo INFRASTOCK',
                'description_english' => 'Agroindustry role of INFRASTOCK module',
                'full_access' => 'No',
                'app_id' => $appId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($roles as $role) {
            // Verificar si el rol ya existe por nombre
            $existing = DB::table('roles')
                ->where('name', $role['name'])
                ->where('app_id', $appId)
                ->first();
            
            if (!$existing) {
                DB::table('roles')->insert($role);
                \Log::info('Rol creado: ' . $role['name'] . ' (app_id: ' . $appId . ')');
            } else {
                // Si existe pero con otro app_id, actualizarlo
                if ($existing->app_id != $appId) {
                    DB::table('roles')
                        ->where('id', $existing->id)
                        ->update(['app_id' => $appId]);
                    \Log::info('Rol actualizado: ' . $role['name'] . ' (app_id cambiado a: ' . $appId . ')');
                } else {
                    \Log::info('Rol ya existe: ' . $role['name'] . ' (app_id: ' . $appId . ')');
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $appId = DB::table('apps')->where('name', 'INFRASTOCK')->value('id');
        
        if ($appId) {
            $roles = ['Ganadería', 'Centro de Convivencia', 'Vigilancia', 'Agroindustria'];
            
            foreach ($roles as $roleName) {
                DB::table('roles')
                    ->where('name', $roleName)
                    ->where('app_id', $appId)
                    ->delete();
            }
        }
    }
}

