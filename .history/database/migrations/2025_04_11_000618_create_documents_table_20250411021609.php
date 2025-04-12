use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('document_type', ['releve_notes', 'attestation_scolarite', 'certificat']);
            $table->string('file_path');
            $table->string('file_name'); // Modification clé ici
            $table->boolean('is_secured')->default(true);
            $table->integer('download_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Correction spécifique pour file_name
        Schema::table('documents', function (Blueprint $table) {
            $table->string('file_name')->nullable(false)->change(); // Rend la colonne obligatoire
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};