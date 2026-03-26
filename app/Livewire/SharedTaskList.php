<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SharedTaskList extends Component
{
     #[Computed]
    public function sharedTasks()
    {
        return Task::whereHas('shares', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['user', 'shares' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->latest()
            ->get();
        // with('user') = eager load the task owner
        // with('shares'...) = eager load this user's share record to get role
    }

    public function render()
    {
        return view('livewire.shared-task-list');
    }
}
