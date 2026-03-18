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
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['to_do', 'in_progress', 'in_review', 'completed']),
            'priority' => $this->faker->numberBetween(1,5),
            'due_date' => $this->faker->optional()->dateTimeBetween('now','+14 days'),

        ];
    }
}
