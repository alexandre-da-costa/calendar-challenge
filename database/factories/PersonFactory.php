<?php

namespace Database\Factories;

use App\Models\ClientCompany;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = ClientCompany::inRandomOrder()->first(['id', 'name']) ?? ClientCompany::factory()->create();
        do {
            $firstName = $this->faker->firstName;
        } while ($company->people()->where('first_name', $firstName)->exists());

        return [
            'first_name' => $firstName,
            'last_name' => $this->faker->lastName,
            'email' => Str::lower($firstName).'@'.Str::lower($company->name).'.com',
            'title' => $this->faker->jobTitle,
            'avatar_url' => $this->faker->imageUrl(),
            'linkedin_profile_url' => 'https://www.linkedin.com/in/'.$this->faker->slug,
            'company_id' => $company->id,
        ];
    }
}
