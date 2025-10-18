<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixDocumentTypeColumnInPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            // Cambiar el tamaño de la columna document_type para permitir valores más largos
            $table->string('document_type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            // Revertir el cambio de tamaño de la columna document_type
            $table->string('document_type', 10)->change();
        });
    }
}
