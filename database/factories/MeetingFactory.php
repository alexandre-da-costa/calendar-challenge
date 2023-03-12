<?php

namespace Database\Factories;

use App\Models\ClientCompany;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = Carbon::make($this->faker->dateTimeBetween('-1 month', '+10 days'));
        $clientCompany = ClientCompany::inRandomOrder()->first(['id', 'name']) ?? ClientCompany::factory()->create();

        return [
            'meeting_id' => $this->faker->numberBetween(1, 1000),
            'title' => config('company.name').' x '.$clientCompany->name,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes($this->faker->randomElement([30, 60, 90])),
            'updated_at' => $startsAt->copy()->subDays($this->faker->numberBetween(1, 10)),
        ];
    }
}
