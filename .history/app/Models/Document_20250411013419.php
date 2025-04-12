<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'file_path',
        'file_size',
        'is_verified'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Types de documents autorisés
    public static function allowedTypes()
    {
        return [
            'releve_notes' => 'Relevé de Notes',
            'attestation' => 'Attestation de Scolarité',
            'certificat' => 'Certificat de Formation'
        ];
    }
}