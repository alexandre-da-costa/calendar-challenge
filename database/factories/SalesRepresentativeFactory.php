<?php

namespace Database\Factories;

use App\Models\SalesRepresentative;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesRepresentative>
 */
class SalesRepresentativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do {
            $name = $this->faker->name;
        } while (SalesRepresentative::where('name', $name)->exists());

        return [
            'name' => $name,
            'email' => \Str::lower($name).'@'.config('company.name').'.com',
            'meetings_synced_at' => null,
            'calendar_api_key' => $this->faker->password(15, 15),
        ];
    }
}
