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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
