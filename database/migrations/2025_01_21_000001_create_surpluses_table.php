<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surpluses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('surplus_amount'); // Cantidad que sobró
            $table->text('reason'); // Causa por la que sobró
            $table->date('surplus_date'); // Fecha del sobrante
            $table->timestamps();
            
            $table->index(['equipment_id', 'surplus_date']);
            $table->index(['user_id', 'surplus_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surpluses');
    }
};
