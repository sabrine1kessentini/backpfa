use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_mode'); // Espèce, Chèque, Carte, Virement, etc.
            $table->decimal('amount', 10, 2); // Montant avec 2 décimales
            $table->string('reference')->nullable(); // Référence de paiement
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};