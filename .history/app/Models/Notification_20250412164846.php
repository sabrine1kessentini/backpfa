<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'message'
        // Optionnel - supprimez si vous ne voulez pas suivre l'expéditeur
    ];

    public function targets()
    {
        return $this->hasMany(NotificationTarget::class);
    }


}