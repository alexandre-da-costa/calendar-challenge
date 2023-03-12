<?php

namespace App\Traits;

use App\Models\Meeting;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @method morphToMany(string $related, string $name, string $table = null) : MorphToMany
 */
trait HasMeetings
{
    public function meetings(): MorphToMany
    {
        return $this->morphToMany(
            Meeting::class,
            Meeting::ATTENDEE_RELATION_NAME,
            Meeting::ATTENDEE_PIVOT_TABLE_NAME
        );
    }
}
