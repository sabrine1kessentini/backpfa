<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'type',
        'user_id',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    public function targets()
    {
        return $this->hasMany(NotificationTarget::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}