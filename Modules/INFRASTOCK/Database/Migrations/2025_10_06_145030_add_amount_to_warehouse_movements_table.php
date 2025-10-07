<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @class AddAmountToWarehouseMovementsTable
 * @brief Migración para añadir la columna `amount` a la tabla `warehouse_movements` del módulo INFRASTOCK.
 *
 * Esta migración añade el campo `amount` a la tabla `warehouse_movements`, lo que permite
 * registrar la cantidad de insumos o herramientas involucradas en un movimiento de almacén.
 */
return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade la columna `amount` (tipo integer, nullable) a la tabla `warehouse_movements`.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->integer('amount')->nullable()->after('role'); // Añade la columna `amount` después de `role`.
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la columna `amount` de la tabla `warehouse_movements`.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->dropColumn('amount'); // Elimina la columna `amount`.
        });
    }
};
