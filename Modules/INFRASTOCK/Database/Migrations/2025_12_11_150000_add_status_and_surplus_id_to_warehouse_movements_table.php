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
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->nullable()->default('pending')->after('amount');
            $table->foreignId('surplus_id')->nullable()->after('status')->constrained('surpluses')->onDelete('cascade');
            $table->text('description')->nullable()->after('surplus_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            $table->dropForeign(['surplus_id']);
            $table->dropColumn(['status', 'surplus_id', 'description']);
        });
    }
};

