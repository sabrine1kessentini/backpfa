<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('notification_targets', function (Blueprint $table) {
            $table->id(); // Colonne 'id' bigint unsigned auto-increment
            $table->foreignId('notification_id')->constrained()->onDelete('cascade'); // Clé étrangère
            $table->enum('target_type', ['level', 'filiere', 'all']); // Enumération des types
            $table->string('target_value')->nullable(); // Valeur nullable
            $table->timestamps(); // Colonnes created_at et updated_at
            
            // Index pour optimiser les requêtes de filtrage
            $table->index(['target_type', 'target_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('notification_targets');
    }
};