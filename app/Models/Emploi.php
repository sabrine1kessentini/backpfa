<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emploi extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'emploi';

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'groupe',
        'image_path'
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $appends = ['image_url'];

    // Observer pour détecter les changements d'image_path
    protected static function booted()
    {
        static::updated(function ($emploi) {
            if ($emploi->isDirty('image_path')) {
                self::createNotification($emploi);
            }
        });
    }

    // Méthode statique pour créer une notification
    public static function createNotification($emploi)
    {
        try {
            // Créer une notification
            $notification = \App\Models\Notification::create([
                'message' => "L'emploi du temps a été mis à jour",
                'user_id' => auth()->id() ?? 1, // Utiliser l'ID 1 si aucun utilisateur n'est connecté
                'type' => 'emploi_update'
            ]);

            // Ajouter les cibles de la notification
            $notification->targets()->create([
                'target_type' => 'groupe',
                'target_value' => $emploi->groupe
            ]);

            // Ajouter une notification pour tous les étudiants
            $notification->targets()->create([
                'target_type' => 'all',
                'target_value' => null
            ]);

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur lors de la création de la notification: " . $e->getMessage());
            return false;
        }
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('images/emploi/'.$this->image_path) : null;
    }
}