<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompleteAttributesToToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tools', function (Blueprint $table) {
            // Agregar los nuevos campos requeridos
            $table->string('imagen')->nullable()->after('nombre');
            $table->string('marca')->nullable()->after('imagen');
            $table->string('modelo')->nullable()->after('marca');
            $table->string('descripcion')->nullable()->after('modelo'); // Campo adicional para descripción general
            $table->enum('estado', ['disponible', 'en_prestamo', 'mantenimiento', 'no_disponible'])->default('disponible')->after('descripcion');
            $table->integer('cantidad_total')->nullable()->after('estado');
            $table->integer('cantidad_disponible')->nullable()->after('cantidad_total');
            $table->date('fecha_mantenimiento')->nullable()->after('cantidad_disponible');
            $table->date('proximo_mantenimiento')->nullable()->after('fecha_mantenimiento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tools', function (Blueprint $table) {
            // Eliminar los campos agregados
            $table->dropColumn([
                'imagen',
                'marca',
                'modelo',
                'descripcion',
                'estado',
                'cantidad_total',
                'cantidad_disponible',
                'fecha_mantenimiento',
                'proximo_mantenimiento'
            ]);
        });
    }
}
