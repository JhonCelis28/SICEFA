<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @class AddNameToEquipmentsTable
 * @brief Migración para añadir la columna `name` a la tabla `equipments` del módulo INFRASTOCK.
 *
 * Esta migración añade el campo `name` a la tabla `equipments`, lo que permite
 * registrar el nombre descriptivo de cada insumo o equipo.
 */
return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade la columna `name` (tipo string) a la tabla `equipments` después de `inventory_id`.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->string('name')->after('inventory_id'); // Añade la columna `name` después de `inventory_id`.
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la columna `name` de la tabla `equipments`.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn('name'); // Elimina la columna `name`.
        });
    }
};
