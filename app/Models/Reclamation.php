<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status'
    ];

    /**
     * Get the user that owns the reclamation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 