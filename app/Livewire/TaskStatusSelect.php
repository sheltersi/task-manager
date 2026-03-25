<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class TaskStatusSelect extends Component
{
    public Task $task;
    public string $status;

    public function mount(Task $task): void
    {
        Gate::authorize('update', $task);
        $this->status = $task->status;
        $this->status = $task->status;
    }

    public function updatedStatus(): void
    {
        Gate::authorize('update', $this->task);
        $this->task->update(['status' => $this->status]);
        $this->dispatch('task-status-updated');
    }
    public function render()
    {
        return view('livewire.task-status-select');
    }
}
