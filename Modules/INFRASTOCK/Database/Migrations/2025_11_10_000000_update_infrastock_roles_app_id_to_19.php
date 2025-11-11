<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateInfrastockRolesAppIdTo19 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Actualizar los roles de INFRASTOCK de app_id 23 a app_id 19
        $roles = ['Operario', 'Aseo', 'Centro de Convivencia', 'Ganadería'];
        
        foreach ($roles as $roleName) {
            DB::table('roles')
                ->where('name', $roleName)
                ->where('app_id', 23)
                ->update(['app_id' => 19]);
        }
        
        // Log para verificación
        \Log::info('Migración: Roles de INFRASTOCK actualizados de app_id 23 a app_id 19');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir los roles de INFRASTOCK de app_id 19 a app_id 23
        $roles = ['Operario', 'Aseo', 'Centro de Convivencia', 'Ganadería'];
        
        foreach ($roles as $roleName) {
            DB::table('roles')
                ->where('name', $roleName)
                ->where('app_id', 19)
                ->update(['app_id' => 23]);
        }
        
        \Log::info('Migración revertida: Roles de INFRASTOCK actualizados de app_id 19 a app_id 23');
    }
}

