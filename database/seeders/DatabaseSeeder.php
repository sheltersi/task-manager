<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)
        ->has(Task::factory()->count(5))
        ->create();

        // Creates my specific test user, also with 5 tasks
        User::factory()
        ->has(Task::factory()->count(15))
        ->create([
            'name' => 'Test User',
            'email' => 'sheltersibanda001@gmail.com',
            'password' => bcrypt('@Secret123'),
        ]);

        User::create([
            'name' => 'sher',
            'email' => 'sher@gmail.com',
             'password' => bcrypt('Secret123'),
        ]);


    }
}
