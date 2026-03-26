<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class TaskPrioritySelect extends Component
{
    public Task $task;
    public int $priority;

      public function mount(Task $task): void
    {
        Gate::authorize('update', $task);
        $this->task     = $task;
        $this->priority = $task->priority;
    }

      public function setPriority(int $value): void
    {
        Gate::authorize('update', $this->task);
        $this->priority = $value;
        $this->task->update(['priority' => $this->priority]);
          // Tell the parent to update stats without re-rendering everything
        $this->dispatch('update-stats');
    }
        public function render()
    {
        return view('livewire.task-priority-select');
    }
}
