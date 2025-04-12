<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'document_type',
        'file_path',
        'file_name',
        'is_secured',
        'download_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}