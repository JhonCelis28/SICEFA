<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @class AddDeletedAtToInfrastockCategoriesTable
 * @brief Migración para añadir soporte de Soft Deletes a la tabla `infrastock_categories`.
 *
 * Esta migración añade la columna `deleted_at` a la tabla `infrastock_categories`,
 * lo que permite implementar el borrado lógico (soft deletes) para las categorías.
 * Esto significa que los registros no se eliminan físicamente de la base de datos,
 * sino que se marcan como eliminados, conservando el historial.
 */
return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade la columna `deleted_at` a la tabla `infrastock_categories` para habilitar el borrado lógico.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('infrastock_categories', function (Blueprint $table) {
            $table->softDeletes(); // Añade la columna `deleted_at`.
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la columna `deleted_at` de la tabla `infrastock_categories`, deshaciendo el borrado lógico.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('infrastock_categories', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Elimina la columna `deleted_at`.
        });
    }
};
