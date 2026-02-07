<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixDocumentTypeColumnInPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Desactivar temporalmente el modo estricto de SQL para poder corregir datos inválidos
        DB::statement('SET SQL_MODE = ""');
        
        // Corregir datos inválidos en la tabla people (fechas '0000-00-00')
        DB::statement("UPDATE people SET date_of_issue = NULL WHERE date_of_issue = '0000-00-00' OR date_of_issue = '0000-00-00 00:00:00'");
        DB::statement("UPDATE people SET date_of_birth = NULL WHERE date_of_birth = '0000-00-00' OR date_of_birth = '0000-00-00 00:00:00'");
        
        // Cambiar el tamaño de la columna document_type para permitir valores más largos
        // Usando SQL directo para evitar problemas de compatibilidad con Doctrine DBAL
        DB::statement('ALTER TABLE people MODIFY COLUMN document_type VARCHAR(50)');
        
        // Restaurar el modo estricto de SQL
        DB::statement('SET SQL_MODE = "ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir el cambio de tamaño de la columna document_type
        DB::statement('ALTER TABLE people MODIFY COLUMN document_type VARCHAR(10)');
    }
}
