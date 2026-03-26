<?php

namespace App\Livewire;

use App\Models\Subtask;
use App\Models\Task;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SubtaskForm extends Component
{
    public Task $task;

    #[Validate('required|string|max:255')]
    public string $title = '';

    public function mount(Task $task): void
    {
        $this->task = $task;
    }

    public function addSubtask(): void
    {
        $this->validate();

        $this->task->subtasks()->create([
            'title' => $this->title,
        ]);

        $this->title = '';
    }

    public function toggleSubtask(Subtask $subtask): void
    {
        $subtask->update([
            'is_completed' => ! $subtask->is_completed,
        ]);
    }

    public function deleteSubtask(Subtask $subtask): void
    {
        $subtask->delete();
    }

    public function render()
    {
        return view('livewire.subtask-form', [
            'subtasks' => $this->task->subtasks()->get(),
        ]);
    }
}
