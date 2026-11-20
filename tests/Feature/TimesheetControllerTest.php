<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\Worklog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimesheetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_timesheets(): void
    {
        $this->get(route('timesheets.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_weekly_timesheet_grid_and_timeline(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'Timesheet Grid Integration']);
        $today = Carbon::today()->format('Y-m-d');

        Worklog::factory()->for($user)->for($task)->create([
            'work_date' => $today,
            'hours' => 3.5,
            'description' => 'Implemented weekly matrix cells',
            'billable' => true,
        ]);

        $gridResponse = $this->actingAs($user)->get(route('timesheets.index'));
        $gridResponse->assertStatus(200);
        $gridResponse->assertSee('Weekly Timesheet');
        $gridResponse->assertSee('Timesheet Grid Integration');
        $gridResponse->assertSee('3.5h');

        $listResponse = $this->actingAs($user)->get(route('timesheets.index', ['view' => 'list']));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('Implemented weekly matrix cells');
    }

    public function test_user_can_create_update_and_delete_worklog(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();
        $workDate = Carbon::today()->format('Y-m-d');

        $storeResponse = $this->actingAs($user)->postJson(route('timesheets.worklogs.store'), [
            'task_id' => $task->id,
            'work_date' => $workDate,
            'hours' => 2.25,
            'started_at' => '09:30',
            'description' => 'Initial Tempo UI implementation',
            'billable' => true,
        ]);

        $storeResponse->assertStatus(201);
        $this->assertDatabaseHas('worklogs', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'hours' => 2.25,
            'billable' => true,
        ]);

        $worklog = Worklog::firstOrFail();

        $updateResponse = $this->actingAs($user)->putJson(route('timesheets.worklogs.update', $worklog), [
            'task_id' => $task->id,
            'work_date' => $workDate,
            'hours' => 4.0,
            'started_at' => '10:00',
            'description' => 'Refined matrix totals and modal',
            'billable' => false,
        ]);

        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('worklogs', [
            'id' => $worklog->id,
            'hours' => 4.0,
            'billable' => false,
        ]);

        $deleteResponse = $this->actingAs($user)->deleteJson(route('timesheets.worklogs.destroy', $worklog));
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('worklogs', ['id' => $worklog->id]);
    }

    public function test_user_cannot_modify_or_delete_another_users_worklog(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();
        $worklog = Worklog::factory()->for($owner)->for($task)->create();

        $this->actingAs($otherUser)
            ->putJson(route('timesheets.worklogs.update', $worklog), [
                'task_id' => $task->id,
                'work_date' => Carbon::today()->format('Y-m-d'),
                'hours' => 1.5,
            ])
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->deleteJson(route('timesheets.worklogs.destroy', $worklog))
            ->assertForbidden();
    }

    public function test_user_can_export_weekly_timesheet_csv(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'CSV Export Verification']);
        $today = Carbon::today()->format('Y-m-d');

        Worklog::factory()->for($user)->for($task)->create([
            'work_date' => $today,
            'hours' => 2.5,
            'started_at' => '11:00',
            'description' => 'Verified streamed CSV download',
            'billable' => true,
        ]);

        $response = $this->actingAs($user)->get(route('timesheets.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('CSV Export Verification', $content);
        $this->assertStringContainsString('2.50', $content);
    }
}
