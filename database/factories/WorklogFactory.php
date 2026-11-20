<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Worklog>
 */
class WorklogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'work_date' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'hours' => fake()->randomElement([0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 4.0]),
            'started_at' => fake()->randomElement(['09:00', '10:30', '13:30', '15:00']),
            'description' => fake()->sentence(8),
            'billable' => fake()->boolean(80),
        ];
    }
}
