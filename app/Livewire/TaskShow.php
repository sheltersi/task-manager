<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class TaskShow extends Component
{
     public Task $task;

 public function mount(Task $task): void
    {
        Gate::authorize('view', $task);
        $this->task = $task;
    }
    public function delete(): void
{
    Gate::authorize('delete', $this->task);

    $this->task->delete();

    $this->redirect(route('dashboard'), navigate: true);
}


    public function render()
    {

        return view('livewire.task-show');
    }
}
