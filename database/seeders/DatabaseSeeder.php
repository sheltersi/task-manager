<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Worklog;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory(10)
            ->has(Task::factory()->count(5))
            ->create();

        $testUser = User::factory()
            ->has(Task::factory()->count(15))
            ->create([
                'name' => 'Test User',
                'email' => 'sheltersibanda001@gmail.com',
                'position' => 'Lead Developer',
                'password' => bcrypt('@Secret123'),
            ]);

        $sherUser = User::create([
            'name' => 'sher',
            'email' => 'sher@gmail.com',
            'position' => 'Software Engineer',
            'password' => bcrypt('Secret123'),
        ]);

        // Seed weekly worklogs for Test User
        $monday = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $tasks = $testUser->tasks()->take(6)->get()->values();
        if ($tasks->isNotEmpty()) {
            $entries = [
                [0, 3.5, '09:00', true, 'Facilitated sprint backlog grooming with product leads'],
                [0, 2.5, '13:30', true, 'Implemented task ownership policy checks across CRUD routes'],
                [1, 3.0, '09:30', true, 'Reviewed pagination query-string persistence PR'],
                [2, 3.5, '09:00', true, 'Profiled slow task filter queries and composite indexes'],
                [3, 2.5, '11:00', true, 'Added feature test coverage for unauthorized task access'],
                [4, 2.0, '09:00', false, 'Compiled weekly engineering sync notes'],
            ];

            foreach ($entries as $idx => [$offset, $hours, $startedAt, $billable, $desc]) {
                $task = $tasks[$idx % $tasks->count()];
                Worklog::create([
                    'user_id' => $testUser->id,
                    'task_id' => $task->id,
                    'work_date' => $monday->copy()->addDays($offset)->format('Y-m-d'),
                    'hours' => $hours,
                    'started_at' => $startedAt,
                    'billable' => $billable,
                    'description' => $desc,
                ]);
            }
        }

        $sherTask = Task::factory()->create(['user_id' => $sherUser->id]);
        Worklog::create([
            'user_id' => $sherUser->id,
            'task_id' => $sherTask->id,
            'work_date' => $monday->format('Y-m-d'),
            'hours' => 3.0,
            'started_at' => '09:00',
            'billable' => true,
            'description' => 'Configured local workspace and initial sprint board',
        ]);
    }
}
