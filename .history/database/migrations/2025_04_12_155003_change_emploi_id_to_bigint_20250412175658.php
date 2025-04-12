<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('emploi', function (Blueprint $table) {
            // Supprimez d'abord l'auto-incrément et la clé primaire
            DB::statement('ALTER TABLE emploi MODIFY id INT NOT NULL');
            DB::statement('ALTER TABLE emploi DROP PRIMARY KEY');
            
            // Changez le type et recréez la clé primaire
            DB::statement('ALTER TABLE emploi MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        });
    }
    
    public function down()
    {
        Schema::table('emploi', function (Blueprint $table) {
            DB::statement('ALTER TABLE emploi MODIFY id INT NOT NULL');
            DB::statement('ALTER TABLE emploi DROP PRIMARY KEY');
            DB::statement('ALTER TABLE emploi MODIFY id INT AUTO_INCREMENT PRIMARY KEY');
        });
    }
};