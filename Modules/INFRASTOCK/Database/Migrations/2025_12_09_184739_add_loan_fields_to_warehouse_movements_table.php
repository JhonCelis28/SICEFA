<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoanFieldsToWarehouseMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->text('purpose')->nullable()->after('description')
                  ->comment('Finalidad del préstamo');
            $table->date('required_date')->nullable()->after('purpose')
                  ->comment('Fecha requerida para el préstamo');
            $table->string('delivery_image')->nullable()->after('imagen')
                  ->comment('Foto de cómo se entrega la herramienta');
            $table->string('return_image')->nullable()->after('delivery_image')
                  ->comment('Foto de cómo se devuelve la herramienta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'required_date', 'delivery_image', 'return_image']);
        });
    }
}
