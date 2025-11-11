<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateInfrastockAppIdTo19 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Obtener el ID actual de INFRASTOCK
        $currentId = DB::table('apps')->where('name', 'INFRASTOCK')->value('id');
        
        if (!$currentId) {
            throw new \Exception('No se encontró la aplicación INFRASTOCK en la tabla apps');
        }
        
        // Si ya tiene el ID 19, no hacer nada
        if ($currentId == 19) {
            return;
        }
        
        // Verificar si el ID 19 ya está en uso
        $existingApp = DB::table('apps')->where('id', 19)->first();
        if ($existingApp) {
            throw new \Exception('El ID 19 ya está en uso por la aplicación: ' . $existingApp->name);
        }
        
        // Deshabilitar temporalmente las restricciones de claves foráneas
        Schema::disableForeignKeyConstraints();
        
        // Obtener los datos del registro actual
        $appData = DB::table('apps')->where('id', $currentId)->first();
        
        // Actualizar todas las referencias en las tablas relacionadas primero
        DB::table('app_productive_units')
            ->where('app_id', $currentId)
            ->update(['app_id' => 19]);
        
        DB::table('permissions')
            ->where('app_id', $currentId)
            ->update(['app_id' => 19]);
        
        DB::table('roles')
            ->where('app_id', $currentId)
            ->update(['app_id' => 19]);
        
        DB::table('warehouses')
            ->where('app_id', $currentId)
            ->update(['app_id' => 19]);
        
        // Eliminar el registro actual
        DB::table('apps')->where('id', $currentId)->delete();
        
        // Insertar el registro con el ID 19
        DB::table('apps')->insert([
            'id' => 19,
            'name' => $appData->name,
            'url' => $appData->url,
            'color' => $appData->color,
            'icon' => $appData->icon,
            'description' => $appData->description,
            'description_english' => $appData->description_english,
            'deleted_at' => $appData->deleted_at,
            'created_at' => $appData->created_at,
            'updated_at' => $appData->updated_at,
        ]);
        
        // Rehabilitar las restricciones de claves foráneas
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Obtener el ID actual (debería ser 19)
        $currentId = DB::table('apps')->where('name', 'INFRASTOCK')->value('id');
        
        if ($currentId != 19) {
            // Si no es 19, no hacer nada en el rollback
            return;
        }
        
        // Obtener los datos del registro actual
        $appData = DB::table('apps')->where('id', 19)->first();
        
        if (!$appData) {
            return;
        }
        
        // Deshabilitar temporalmente las restricciones de claves foráneas
        Schema::disableForeignKeyConstraints();
        
        // Buscar un ID disponible (el siguiente disponible)
        $nextId = DB::table('apps')->max('id') + 1;
        
        // Actualizar todas las referencias en las tablas relacionadas
        DB::table('app_productive_units')
            ->where('app_id', 19)
            ->update(['app_id' => $nextId]);
        
        DB::table('permissions')
            ->where('app_id', 19)
            ->update(['app_id' => $nextId]);
        
        DB::table('roles')
            ->where('app_id', 19)
            ->update(['app_id' => $nextId]);
        
        DB::table('warehouses')
            ->where('app_id', 19)
            ->update(['app_id' => $nextId]);
        
        // Eliminar el registro actual
        DB::table('apps')->where('id', 19)->delete();
        
        // Insertar el registro con el nuevo ID automático
        DB::table('apps')->insert([
            'id' => $nextId,
            'name' => $appData->name,
            'url' => $appData->url,
            'color' => $appData->color,
            'icon' => $appData->icon,
            'description' => $appData->description,
            'description_english' => $appData->description_english,
            'deleted_at' => $appData->deleted_at,
            'created_at' => $appData->created_at,
            'updated_at' => $appData->updated_at,
        ]);
        
        // Rehabilitar las restricciones de claves foráneas
        Schema::enableForeignKeyConstraints();
    }
}
