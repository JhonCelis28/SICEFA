<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitialAmountToEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->integer('initial_amount')->default(0)->after('amount')->comment('Cantidad inicial cuando se compró el insumo');
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
            $table->dropColumn('initial_amount');
        });
    }
}
