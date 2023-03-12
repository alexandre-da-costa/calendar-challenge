<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentEmail extends Model
{
    const CREATED_AT = null;

    const UPDATED_AT = null;

    protected $fillable = [
        'email',
        'body',
        'sent_at',
    ];

    protected $casts = [
        'body' => 'json',
    ];
}
