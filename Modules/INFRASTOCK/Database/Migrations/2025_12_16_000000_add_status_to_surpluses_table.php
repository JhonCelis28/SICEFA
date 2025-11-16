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
        Schema::table('surpluses', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('surplus_date');
            $table->timestamp('processed_at')->nullable()->after('status');
            $table->string('processed_by')->nullable()->after('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surpluses', function (Blueprint $table) {
            $table->dropColumn(['status', 'processed_at', 'processed_by']);
        });
    }
};

