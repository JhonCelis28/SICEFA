<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeNotificationsIdToUuid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Eliminar registros existentes si los hay (opcional, comentar si hay datos importantes)
        // DB::table('notifications')->truncate();
        
        // Eliminar la clave primaria y el auto-incremento
        DB::statement('ALTER TABLE notifications MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE notifications DROP PRIMARY KEY');
        
        // Cambiar la columna id de bigint a char(36) para UUIDs
        DB::statement('ALTER TABLE notifications MODIFY id CHAR(36) NOT NULL');
        
        // Restaurar la clave primaria
        DB::statement('ALTER TABLE notifications ADD PRIMARY KEY (id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la clave primaria
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });
        
        // Cambiar la columna id de char(36) a bigint unsigned auto-incremental
        DB::statement('ALTER TABLE notifications MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        
        // Restaurar la clave primaria
        Schema::table('notifications', function (Blueprint $table) {
            $table->primary('id');
        });
    }
}
