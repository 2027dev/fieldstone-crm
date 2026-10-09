<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(array_keys(Activity::TYPES)),
            'subject' => fake()->sentence(4),
            'due_date' => fake()->dateTimeBetween('-1 week', '+2 weeks'),
            'done' => fake()->boolean(30),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High']),
            'contact_id' => Contact::factory(),
        ];
    }
}
