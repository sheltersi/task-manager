<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskChart extends Component
{
    #[On('task-status-updated')]
    public function refreshStatusChart(): void
    {
        unset($this->statusData); // clears the #[Computed] cache
        $this->dispatch('refresh-status-chart', data: $this->statusData);
    }

      #[Computed]
    public function statusData(): array
    {
        $tasks = Task::where('user_id', Auth::id())->get();

        return [
            'labels' => ['To Do', 'In Progress', 'In Review', 'Completed'],
            'data' => [
                $tasks->where('status', 'to_do')->count(),
                $tasks->where('status', 'in_progress')->count(),
                $tasks->where('status', 'in_review')->count(),
                $tasks->where('status', 'completed')->count(),
            ],
        ];
    }

    #[Computed]
    public function priorityData(): array
    {
        $tasks = Task::where('user_id', Auth::id())->get();

        return [
            'labels' => ['Priority 1', 'Priority 2', 'Priority 3', 'Priority 4', 'Priority 5'],
            'data' => [
                $tasks->where('priority', 1)->count(),
                $tasks->where('priority', 2)->count(),
                $tasks->where('priority', 3)->count(),
                $tasks->where('priority', 4)->count(),
                $tasks->where('priority', 5)->count(),
            ],
        ];
    }



    public function render()
    {
        return view('livewire.task-chart');
    }
}
