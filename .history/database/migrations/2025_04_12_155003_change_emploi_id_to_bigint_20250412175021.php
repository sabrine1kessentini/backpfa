<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pour MySQL
        DB::statement('ALTER TABLE emploi MODIFY id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY');
        
        // Alternative plus portable (si vous utilisez plusieurs SGBD)
        Schema::table('emploi', function (Blueprint $table) {
            $table->bigIncrements('id')->change(); // BIGINT UNSIGNED auto-incrémenté
        });
    }

    public function down()
    {
        // Pour annuler la migration
        DB::statement('ALTER TABLE emploi MODIFY id INT AUTO_INCREMENT PRIMARY KEY');
        
        // Alternative portable
        Schema::table('emploi', function (Blueprint $table) {
            $table->integer('id', true)->change(); // Retour à INT
        });
    }
};