use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['releve_notes', 'attestation', 'certificat']);
            $table->string('title');
            $table->string('file_path');
            $table->unsignedInteger('file_size'); // Taille en octets
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};