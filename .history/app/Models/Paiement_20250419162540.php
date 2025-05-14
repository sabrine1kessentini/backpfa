// app/Models/Paiement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}