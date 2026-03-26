<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskForm extends Component
{
    use WithFileUploads;

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

    #[Validate('nullable|image|max:2048')]
    public $image = null;

    public ?string $imagePreview = null;

    public function mount(?Task $task = null): void
    {
        if ($task) {
            Gate::authorize('update', $task);
            $this->task = $task;
            $this->title = $task->title;
            $this->description = $task->description ?? '';
            $this->status = $task->status;
            $this->priority = $task->priority;
            $this->due_date = $task->due_date?->format('Y-m-d');
        }
    }

    public function updatedImage(): void
    {
        if ($this->image) {
            $this->imagePreview = $this->image->temporaryUrl();
        } else {
            $this->imagePreview = null;
        }
    }

    public function removeImage(): void
    {
        $this->image = null;
        $this->imagePreview = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $imagePath = $this->task?->image_path;
        if ($this->image) {
            $imagePath = $this->image->store('task-images', 'public');
        }

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
            'image_path' => $imagePath,
        ];

        if ($this->task) {
            Gate::authorize('update', $this->task);
            $this->task->update($data);
        } else {
            auth()->user()->tasks()->create($data);
        }
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.task-form');
    }
}
