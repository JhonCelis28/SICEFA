<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @class AddExpirationDateToEquipmentsTable
 * @brief Migración para añadir la columna `expiration_date` a la tabla `equipments` del módulo INFRASTOCK.
 *
 * Esta migración añade el campo `expiration_date` a la tabla `equipments`, permitiendo registrar
 * la fecha de vencimiento de cada insumo.
 */
return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade la columna `expiration_date` (tipo date, nullable) a la tabla `equipments`.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->date('expiration_date')->nullable()->after('price'); // Añade la columna `expiration_date` después de `price`.
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la columna `expiration_date` de la tabla `equipments`.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn('expiration_date'); // Elimina la columna `expiration_date`.
        });
    }
};
