<?php

namespace App\Models;

use App\Traits\HasMeetings;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRepresentative extends Model
{
    use HasFactory, HasMeetings;

    const CREATED_AT = null;

    const UPDATED_AT = null;

    protected $hidden = [
        'meetings_synced_at',
    ];

    public function getMeetingsForDate(DateTimeInterface $date): Collection
    {
        return $this->meetings()
            ->with([
                'salesRepresentatives',
                'people',
                'people.clientCompany',
            ])
            ->whereDate('starts_at', '>=', $date)
            ->where('starts_at', '<', $date->modify('+1 day'))
            ->get();
    }
}
