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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // Colonne 'id' bigint unsigned auto-increment
            $table->text('message'); // Colonne message
            $table->timestamps(); // Colonnes created_at et updated_at
            
            // Si vous avez besoin de sender_id (optionnel)
            // $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};