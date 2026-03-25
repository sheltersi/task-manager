<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TaskForm extends Component
{
      public ?Task $task = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string')]
    public string $description = '';

    #[Validate('required|in:to_do,in_progress,in_review,completed')]
    public string $status = 'to_do';

    #[Validate('required|integer|min:1|max:5')]
    public int $priority = 1;

    #[Validate('nullable|date')]
    public ?string $due_date = null;

    public function mount(?Task $task = null): void
    {
        // $this->task = $task;
        if ($task) {
            Gate::authorize('update', $task);
            // $this->fill($task->only('title', 'description', 'status', 'priority', 'due_date'));
               $this->task = $task;
            $this->title       = $task->title;
            $this->description = $task->description ?? '';
            $this->status      = $task->status;
            $this->priority    = $task->priority;
            $this->due_date    = $task->due_date?->format('Y-m-d');
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

         if ($this->task) {
            Gate::authorize('update', $this->task);
            $this->task->update($validated);
        } else {
            auth()->user()->tasks()->create($validated);
        }
 $this->redirect(route('dashboard'), navigate: true);

    }

    public function render()
    {
        return view('livewire.task-form');
    }
}
