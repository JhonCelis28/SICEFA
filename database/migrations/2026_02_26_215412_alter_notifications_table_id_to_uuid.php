<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotificationsTableIdToUuid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First drop the existing primary key so we can resize it
        try {
            \DB::statement('ALTER TABLE notifications MODIFY id CHAR(36) NOT NULL');
        } catch (\Exception $e) {
            \Log::info("Migration error handling existing UUID format: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // It's dangerous to shrink UUIDs back to ints as data is lost, 
        // but for symmetry of the down method:
        try {
            \DB::statement('ALTER TABLE notifications MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } catch (\Exception $e) {
            \Log::info("Migration error handling UUID rollback: " . $e->getMessage());
        }
    }
}
