<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @class MakeMovementIdNullableInWarehouseMovements
 * @brief Migración para hacer nullable el campo movement_id en warehouse_movements.
 *
 * Esta migración permite que movement_id sea nullable para permitir
 * registros de WarehouseMovement que no están asociados a un Movement tradicional,
 * como en el caso de las solicitudes de insumos en INFRASTOCK.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usar DB::statement para modificar la columna directamente
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE warehouse_movements MODIFY movement_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a NOT NULL
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE warehouse_movements MODIFY movement_id BIGINT UNSIGNED NOT NULL');
    }
};

