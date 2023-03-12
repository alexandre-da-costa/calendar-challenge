<?php

namespace App\Interfaces;

use DateTimeInterface;

interface CalendarInterface
{
    public function syncMeetingsChangedAfter(string $email, DateTimeInterface $changedAfter = null): void;
}
