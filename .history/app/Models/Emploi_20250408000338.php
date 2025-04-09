// app/Models/EmploiDuTemps.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Emploi extends Model {
    protected $table = 'emplois_du_temps';
    protected $fillable = ['groupe', 'image_path'];
}