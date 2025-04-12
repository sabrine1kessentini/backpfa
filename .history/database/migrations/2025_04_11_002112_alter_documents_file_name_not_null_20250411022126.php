use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Pour MySQL
        DB::statement('ALTER TABLE documents MODIFY file_name VARCHAR(255) NOT NULL');

        // Alternative pour tous les SGBD
        Schema::table('documents', function (Blueprint $table) {
            $table->string('file_name')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('file_name')->nullable()->change();
        });
    }
};