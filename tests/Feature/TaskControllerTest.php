<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase; // Resets the database for every test

    /** @test */
    public function an_authenticated_user_can_see_their_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'title' => 'My Task']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('My Task');
    }

    /** @test */
    public function a_user_can_create_a_task()
    {
        $user = User::factory()->create();

        $taskData = [
            'title' => 'New Test Task',
            'description' => 'Testing description',
            'status' => 'to_do',
            'priority' => 3,
            'due_date' => now()->addDays(2)->format('Y-m-d'),
        ];

        $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

        $this->assertDatabaseHas('tasks', ['title' => 'New Test Task']);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function a_user_cannot_view_another_users_task()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskOfUser2 = Task::factory()->create(['user_id' => $user2->id]);

        // User 1 tries to see User 2's task
        $response = $this->actingAs($user1)->get(route('tasks.show', $taskOfUser2));

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function a_user_can_delete_their_own_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $this->assertModelMissing($task); 
        $response->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function filtering_tasks_by_status_works()
    {
        $user = User::factory()->create();
        Task::factory()->create(['user_id' => $user->id, 'status' => 'completed', 'title' => 'Finished Task']);
        Task::factory()->create(['user_id' => $user->id, 'status' => 'to_do', 'title' => 'Pending Task']);

        $response = $this->actingAs($user)->get(route('dashboard', ['status' => 'completed']));

        $response->assertSee('Finished Task');
        $response->assertDontSee('Pending Task');
    }
}
