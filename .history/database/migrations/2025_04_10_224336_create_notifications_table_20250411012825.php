<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', ['releve_notes', 'attestation_scolarite', 'certificat']);
            $table->string('file_path');
            $table->string('file_name');
            $table->boolean('is_secured')->default(true);
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'document_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};