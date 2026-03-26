<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskShare;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskShareSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'sheltersibanda001@gmail.com')->first();
        $tasks = Task::where('user_id', '!=', $user->id)->get();

        $roles = ['viewer', 'commenter'];
        $shuffledTasks = $tasks->shuffle();

        for ($i = 0; $i < 35; $i++) {
            $task = $shuffledTasks[$i % $shuffledTasks->count()];
            TaskShare::firstOrCreate([
                'task_id' => $task->id,
                'user_id' => $user->id,
            ], [
                'role' => $roles[array_rand($roles)],
            ]);
        }
    }
}
