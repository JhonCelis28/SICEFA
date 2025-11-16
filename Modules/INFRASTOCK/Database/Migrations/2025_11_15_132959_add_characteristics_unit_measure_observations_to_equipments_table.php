<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacteristicsUnitMeasureObservationsToEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->text('characteristics')->nullable()->after('name')->comment('Características del insumo');
            $table->string('unit_measure', 50)->nullable()->after('initial_amount')->comment('Unidad de medida (ej: kg, litros, unidades)');
            $table->text('observations')->nullable()->after('expiration_date')->comment('Observaciones adicionales del insumo');
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
            $table->dropColumn(['characteristics', 'unit_measure', 'observations']);
        });
    }
}
