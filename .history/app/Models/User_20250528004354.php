<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory ,Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'filiere',
        'niveau',
        'groupe'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'filiere' => $this->filiere,
            'niveau' => $this->niveau,
            'groupe' => $this->groupe
        ];
    }

    public function documents()
    {
        return $this->hasMany(\App\Models\Document::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

        public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}