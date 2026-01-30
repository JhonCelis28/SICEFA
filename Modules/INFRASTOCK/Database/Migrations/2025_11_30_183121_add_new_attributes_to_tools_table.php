<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewAttributesToToolsTable extends Migration
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
            $table->string('nombre')->after('id');
            $table->text('descripcion_actual')->nullable()->after('nombre');
            $table->string('placa')->nullable()->after('descripcion_actual');
            $table->text('atributos')->nullable()->after('placa');
            $table->date('fecha_adquisicion')->nullable()->after('atributos');
            $table->text('descripcion_mantenimiento')->nullable()->after('fecha_adquisicion');
        });
        
        // Hacer nullable los campos existentes usando SQL directo para evitar problemas con Doctrine DBAL
        DB::statement('ALTER TABLE `tools` MODIFY `inventory_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `labor_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `amount` INT NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `price` DECIMAL(10,2) NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `category_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tools', function (Blueprint $table) {
            // Eliminar los nuevos campos
            $table->dropColumn([
                'nombre',
                'descripcion_actual',
                'placa',
                'atributos',
                'fecha_adquisicion',
                'descripcion_mantenimiento'
            ]);
        });
        
        // Revertir los campos a no nullable usando SQL directo
        // Nota: Esto puede fallar si hay registros con valores null
        DB::statement('ALTER TABLE `tools` MODIFY `inventory_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `labor_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `amount` INT NOT NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `price` DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE `tools` MODIFY `category_id` BIGINT UNSIGNED NULL');
    }
}
