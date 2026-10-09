<?php

namespace Database\Factories;

use App\Models\DealStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DealStage>
 */
class DealStageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'sort_order' => 0,
        ];
    }
}
