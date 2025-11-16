<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('productive_unit_warehouse_id')->nullable()->after('user_id');
            $table->text('description')->nullable()->after('productive_unit_warehouse_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('description');
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->unsignedBigInteger('approved_by')->nullable()->after('rejected_at');
            $table->text('rejection_reason')->nullable()->after('approved_by');
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'user_id',
                'productive_unit_warehouse_id',
                'description',
                'status',
                'approved_at',
                'rejected_at',
                'approved_by',
                'rejection_reason'
            ]);
        });
    }
}
