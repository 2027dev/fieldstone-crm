<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->bs(),
            'contact_id' => Contact::factory(),
            'organization_id' => Organization::factory(),
            'value' => fake()->numberBetween(200, 20000),
            'source' => fake()->randomElement(['Website', 'Referral', 'LinkedIn', 'Cold Call', 'Trade Show']),
            'status' => 'inbox',
        ];
    }
}
