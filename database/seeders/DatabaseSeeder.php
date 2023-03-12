<?php

namespace Database\Seeders;

use App\Models\SalesRepresentative;
use App\Models\UserGems\CalendarApiKey;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SalesRepresentative::upsert([
            ['name' => 'Stephan', 'email' => 'stephan@usergems.com'],
            ['name' => 'Christian',
                'email' => 'christian@usergems.com'],
            ['name' => 'Joss',
                'email' => 'joss@usergems.com'],
            ['name' => 'Blaise',
                'email' => 'blaise@usergems.com'],
        ], 'email');

        CalendarApiKey::upsert([
            ['sales_representative_id' => 1, 'key' => '7S$16U^FmxkdV!1b'],
            ['sales_representative_id' => 2, 'key' => 'Ay@T3ZwF3YN^fZ@M'],
            ['sales_representative_id' => 3, 'key' => 'PK7UBPVeG%3pP9%B'],
            ['sales_representative_id' => 4, 'key' => 'c0R*4iQK21McwLww'],
        ], 'id');
    }
}
