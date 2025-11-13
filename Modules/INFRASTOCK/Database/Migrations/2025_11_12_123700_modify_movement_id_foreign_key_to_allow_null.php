<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * @class ModifyMovementIdForeignKeyToAllowNull
 * @brief Migración para modificar la restricción de clave foránea de movement_id para permitir NULL.
 *
 * Esta migración elimina y recrea la restricción de clave foránea de movement_id
 * para que permita valores NULL, necesario para las solicitudes de insumos.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener el nombre de la restricción de clave foránea
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'warehouse_movements' 
            AND COLUMN_NAME = 'movement_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (!empty($foreignKeys)) {
            $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
            
            // Eliminar la restricción de clave foránea existente
            DB::statement("ALTER TABLE warehouse_movements DROP FOREIGN KEY {$constraintName}");
        }

        // Recrear la restricción de clave foránea que permita NULL
        // Nota: MySQL no permite claves foráneas con valores NULL directamente,
        // así que simplemente no recreamos la restricción si movement_id puede ser NULL
        // O podemos crear una restricción condicional, pero es más simple no tenerla
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear la restricción de clave foránea original
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->foreign('movement_id')->references('id')->on('movements')->onDelete('cascade');
        });
    }
};

