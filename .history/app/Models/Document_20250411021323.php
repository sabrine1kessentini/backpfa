<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'document_type', 
        'file_path',
        'file_name', // Ce champ doit être présent
        'is_secured',
        'download_count'
    ];

    protected $casts = [
        'is_verified' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function allowedTypes(): array
    {
        return [
            'releve_notes' => 'Relevé de Notes',
            'attestation' => 'Attestation de Scolarité',
            'certificat' => 'Certificat de Formation'
        ];
    }
}