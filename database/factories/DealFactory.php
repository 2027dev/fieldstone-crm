<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->catchPhrase(),
            'contact_id' => Contact::factory(),
            'organization_id' => Organization::factory(),
            'deal_stage_id' => DealStage::factory(),
            'value' => fake()->numberBetween(500, 50000),
            'currency' => 'USD',
            'status' => 'open',
            'expected_close_date' => fake()->dateTimeBetween('now', '+3 months'),
        ];
    }

    public function won(): static
    {
        return $this->state(fn () => ['status' => 'won']);
    }

    public function lost(): static
    {
        return $this->state(fn () => ['status' => 'lost']);
    }
}
