<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY
            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->enum('document_type', [
                'releve_notes',
                'attestation_scolarite',
                'certificat'
            ]);
            
            $table->string('file_path'); // varchar(255)
            $table->string('file_name'); // varchar(255)
            $table->boolean('is_secured')->default(true); // tinyint(1) DEFAULT 1
            $table->integer('download_count')->default(0); // int(11) DEFAULT 0
            
            $table->timestamps(); // created_at et updated_at
        });

        // Si vous voulez être parfaitement synchronisé avec votre structure actuelle :
        Schema::table('documents', function (Blueprint $table) {
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
            $table->timestamp('updated_at')
                  ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))
                  ->change();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};