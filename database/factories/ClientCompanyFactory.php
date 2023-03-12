<?php

namespace Database\Factories;

use App\Models\ClientCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientCompany>
 */
class ClientCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyName = $this->faker->company;

        return [
            'name' => $companyName,
            'linkedin_page_url' => 'https://www.linkedin.com/company/'.\Str::slug($companyName),
            'employees_count' => $this->faker->numberBetween(1, 20) * 50,
        ];
    }
}
