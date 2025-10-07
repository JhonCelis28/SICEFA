<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @class AddUserIdAndItemTypeToWarehouseMovementsTable
 * @brief Migración para añadir las columnas `user_id` y `item_type` a la tabla `warehouse_movements`.
 *
 * Esta migración extiende la tabla `warehouse_movements` para incluir una relación
 * con la tabla `users` (mediante `user_id`) y un campo `item_type` que especifica
 * si el movimiento es de un insumo ('equipment') o una herramienta ('tool').
 */
class AddUserIdAndItemTypeToWarehouseMovementsTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade las columnas `user_id` (foreign key, nullable) y `item_type` (string, nullable)
     * a la tabla `warehouse_movements`.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            // Añade la columna `user_id` como clave foránea a la tabla `users`.
            // Es nullable y se establece a null en caso de eliminación del usuario.
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('movement_id');
            // Añade la columna `item_type` para especificar el tipo de elemento del movimiento ('equipment' o 'tool').
            $table->string('item_type')->nullable()->after('user_id');
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina las columnas `user_id` y `item_type` de la tabla `warehouse_movements`.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            // Elimina la clave foránea y la columna `user_id`.
            $table->dropConstrainedForeignId('user_id');
            // Elimina la columna `item_type`.
            $table->dropColumn('item_type');
        });
    }
}
