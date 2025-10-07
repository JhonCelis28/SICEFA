<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @class AddCategoryIdToToolsTable
 * @brief Migración para añadir la columna `category_id` a la tabla `tools` del módulo INFRASTOCK.
 *
 * Esta migración añade el campo `category_id` como clave foránea a la tabla `tools`,
 * estableciendo una relación con la tabla `infrastock_categories`. Esto permite asociar
 * cada herramienta a una categoría específica para una mejor organización.
 */
return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade la columna `category_id` (foreign key, nullable) a la tabla `tools` después de `price`.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            // Añade la columna `category_id` como clave foránea a `infrastock_categories`.
            // Es nullable y se establece a null en caso de eliminación de la categoría.
            $table->foreignId('category_id')->nullable()->after('price')->constrained('infrastock_categories')->onDelete('set null');
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la columna `category_id` de la tabla `tools`.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            // Elimina la clave foránea y la columna `category_id`.
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
