<?php

namespace App\Models\UserGems;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarApiKey extends Model
{
    use HasFactory;

    const CREATED_AT = null;

    const UPDATED_AT = null;

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(
            config('usergems.calendar.owner.model_class_fqn'),
            config('usergems.calendar.owner.id_column_name')
        );
    }
}
