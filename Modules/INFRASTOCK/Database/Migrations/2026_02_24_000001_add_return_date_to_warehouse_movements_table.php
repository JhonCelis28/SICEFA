<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnDateToWarehouseMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_movements', 'return_date')) {
                $table->date('return_date')
                    ->nullable()
                    ->after('required_date')
                    ->comment('Fecha estimada de devolución de la herramienta');
            }
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
            if (Schema::hasColumn('warehouse_movements', 'return_date')) {
                $table->dropColumn('return_date');
            }
        });
    }
}

