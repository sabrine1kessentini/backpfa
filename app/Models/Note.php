<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'user_id',
        'matiere',
        'note',
        'semestre',
        'commentaire'
    ];

    protected static function booted()
    {
        static::created(function ($note) {
            // Créer la notification
            $notification = $note->user->notifications()->create([
                'message' => 'Nouvelle note publiée en ' . $note->matiere . ' !',
                'type' => 'note'
            ]);

            // Cibler tous les utilisateurs
            $notification->targets()->create([
                'target_type' => 'all',
                'target_value' => null
            ]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
