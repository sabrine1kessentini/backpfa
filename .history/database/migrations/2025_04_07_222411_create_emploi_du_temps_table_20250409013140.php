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
        Schema::create('emploi_du_temps', function (Blueprint $table) {
            $table->id();
            $table->string('groupe', 10)->unique(); // 'r', '2' etc.
            $table->string('image_path'); // Chemin vers l'image
            $table->timestamps();
        });

        \DB::table('emploi_du_temps')->insert([
            'groupe' => '4',
            'image_path' => 'emplois/default.jpg',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emploi_du_temps');
    }
};
