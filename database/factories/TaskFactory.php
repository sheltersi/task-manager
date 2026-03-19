<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        return [
            'title' => $faker->sentence(4),
            'description' => $faker->optional()->paragraph(),
            'status' => $faker->randomElement(['to_do', 'in_progress', 'in_review', 'completed']),
            'priority' => $faker->numberBetween(1,5),
            'due_date' => $faker->optional()->dateTimeBetween('now','+14 days'),

        ];
    }
}
