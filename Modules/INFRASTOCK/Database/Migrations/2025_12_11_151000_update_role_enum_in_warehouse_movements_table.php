<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el ENUM de la columna role para incluir 'Préstamo' y 'Devolución'
        DB::statement("ALTER TABLE warehouse_movements MODIFY COLUMN role ENUM('Entrega', 'Recibe', 'Préstamo', 'Devolución') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al ENUM original
        DB::statement("ALTER TABLE warehouse_movements MODIFY COLUMN role ENUM('Entrega', 'Recibe') NOT NULL");
    }
};

