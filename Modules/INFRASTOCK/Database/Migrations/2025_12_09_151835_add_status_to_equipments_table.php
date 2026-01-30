<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->enum('status', ['disponible', 'agotado', 'vencido', 'bajo_stock', 'critico'])
                  ->default('disponible')
                  ->after('expiration_date')
                  ->comment('Estado del insumo: disponible, agotado, vencido, bajo_stock, critico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
