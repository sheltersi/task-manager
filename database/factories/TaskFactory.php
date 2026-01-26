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
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['to_do', 'in_progress', 'in_review', 'completed']),
            'priority' => fake()->numberBetween(1,5),
            'due_date' => fake()->optional()->dateTimeBetween('now','+14 days'),

        ];
    }
}
