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
            $table->foreignId('request_id')->nullable()->after('user_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('request_item_id')->nullable()->after('request_id')->constrained('request_items')->onDelete('cascade');
            $table->text('description')->nullable()->after('reason'); // Descripción del sobrante
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surpluses', function (Blueprint $table) {
            $table->dropForeign(['request_id']);
            $table->dropForeign(['request_item_id']);
            $table->dropColumn(['request_id', 'request_item_id', 'description']);
        });
    }
};

