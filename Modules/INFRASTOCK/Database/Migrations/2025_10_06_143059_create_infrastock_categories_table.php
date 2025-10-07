<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @class CreateInfrastockCategoriesTable
 * @brief Migración para crear la tabla `infrastock_categories` en la base de datos del módulo INFRASTOCK.
 *
 * Esta migración define la estructura de la tabla `infrastock_categories`, que se utiliza
 * para categorizar los insumos y herramientas dentro del sistema. Incluye campos para
 * el nombre de la categoría y su tipo (indicando si es para insumos o herramientas).
 */
class CreateInfrastockCategoriesTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Crea la tabla `infrastock_categories` con los campos `id`, `name`, `type` y timestamps.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infrastock_categories', function (Blueprint $table) {
            $table->id(); // Columna de ID auto-incremental (clave primaria).
            $table->string('name')->unique(); // Nombre de la categoría (único).
            $table->string('type'); // Tipo de categoría (ej. 'supply' para insumos, 'tool' para herramientas).
            $table->timestamps(); // Columnas `created_at` y `updated_at`.
        });
    }

    /**
     * Revierte las migraciones.
     * Elimina la tabla `infrastock_categories` si existe.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infrastock_categories'); // Elimina la tabla `infrastock_categories`.
    }
}
