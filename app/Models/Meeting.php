<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Meeting extends Model
{
    use HasFactory;

    const ATTENDEE_RELATION_NAME = 'attendee';

    const ATTENDEE_PIVOT_TABLE_NAME = 'attendee_meeting';

    const CREATED_AT = null;

    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'title',
        'starts_at',
        'ends_at',
        'updated_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function salesRepresentatives(): MorphToMany
    {
        return $this->morphedByMany(
            SalesRepresentative::class,
            static::ATTENDEE_RELATION_NAME,
            static::ATTENDEE_PIVOT_TABLE_NAME
        )->withPivot('is_accepted');
    }

    public function people(): MorphToMany
    {
        return $this->morphedByMany(
            Person::class,
            static::ATTENDEE_RELATION_NAME,
            static::ATTENDEE_PIVOT_TABLE_NAME
        )->withPivot('is_accepted');
    }
}
