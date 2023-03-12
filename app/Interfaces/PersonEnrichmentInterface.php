<?php

namespace App\Interfaces;

use App\Models\Person;

interface PersonEnrichmentInterface
{
    /**
     * Get enriched person data by the person's email
     */
    public function enrichPersonDataByEmail(string $email): Person;
}
