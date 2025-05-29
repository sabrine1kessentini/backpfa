<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'file_path',
        'file_size'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected static function booted()
    {
        static::created(function ($document) {
            // Créer la notification
            $notification = $document->user->notifications()->create([
                'message' => 'Nouveau document ajouté : ' . $document->title,
                'type' => 'document'
            ]);

            // Cibler tous les utilisateurs
            $notification->targets()->create([
                'target_type' => 'all',
                'target_value' => null
            ]);
        });
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}