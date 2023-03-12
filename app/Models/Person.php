<?php

namespace App\Models;

use App\Traits\HasMeetings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    use HasFactory, HasMeetings;

    const CREATED_AT = null;

    const UPDATED_AT = null;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'title',
        'avatar_url',
        'linkedin_profile_url',
        'client_company_id',
        'last_enriched_at',
    ];

    protected $hidden = [
        'client_company_id',
        'last_enriched_at',
    ];

    protected $casts = [
        'last_enriched_at' => 'datetime',
    ];

    public function clientCompany(): BelongsTo
    {
        return $this->belongsTo(ClientCompany::class);
    }
}
