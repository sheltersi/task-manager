<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_tasks(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'Quarterly Roadmap Review']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee($task->title);
    }

    public function test_user_can_create_and_update_task(): void
    {
        $user = User::factory()->create();

        $createResponse = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Prepare Sprint Backlog',
            'description' => 'Prioritize items for sprint planning',
            'status' => 'to_do',
            'priority' => 4,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $createResponse->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Prepare Sprint Backlog',
        ]);

        $task = Task::where('user_id', $user->id)->firstOrFail();

        $updateResponse = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Prepare Sprint Backlog v2',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'priority' => 5,
            'due_date' => now()->addDays(6)->format('Y-m-d'),
        ]);

        $updateResponse->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Prepare Sprint Backlog v2',
            'status' => 'in_progress',
        ]);
    }

    public function test_user_cannot_modify_another_users_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->actingAs($otherUser)
            ->delete(route('tasks.destroy', $task))
            ->assertForbidden();
    }
}
