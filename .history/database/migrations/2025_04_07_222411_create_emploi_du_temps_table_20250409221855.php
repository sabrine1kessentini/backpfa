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
        Schema::create('emploi', function (Blueprint $table) {
            $table->id();
            $table->string('groupe', 10)->unique(); // 'r', '2' etc.
            $table->string('image_path'); // Chemin vers l'image
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('emploi');
    }
};