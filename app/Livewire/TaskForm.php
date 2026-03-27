<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
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
            $this->task = $task;

            $this->task = $task;
            $this->fill([
                'title' => $task->title,
                'description' => $task->description ?? '',
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->format('Y-m-d'),
            ]);
            // ✅ Add this line
            if ($task->image_path) {
                $this->imagePreview = Storage::disk('public')->url($task->image_path);
            }
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
        // ✅ Delete the stored image if editing an existing task
        if ($this->task && $this->task->image_path) {
            Storage::disk('public')->delete($this->task->image_path);
            $this->task->update(['image_path' => null]);
        }

        $this->image = null;
        $this->imagePreview = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->task) {
            Gate::authorize('update', $this->task);

            $imagePath = $this->task->image_path;

            if ($this->image) {
                if ($this->task->image_path) {
                    Storage::disk('public')->delete($this->task->image_path);
                }
                $imagePath = $this->image->store('task-images', 'public');
            }

            $this->task->update([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status'      => $validated['status'],
                'priority'    => $validated['priority'],
                'due_date'    => $validated['due_date'] ?? null,
                'image_path'  => $imagePath,
            ]);
        } else {

            $imagePath = null;

            if ($this->image) {
                $imagePath = $this->image->store('task-images', 'public');
            }

            auth()->user()->tasks()->create([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status'      => $validated['status'],
                'priority'    => $validated['priority'],
                'due_date'    => $validated['due_date'] ?? null,
                'image_path'  => $imagePath,
            ]);
        }

        $this->redirect(route('dashboard'), navigate: true);
    }
    public function cancel(): void
    {
        // Clean up temp image if it exists and task was never saved
        if ($this->image && !$this->task) {
            Storage::delete('private/livewire-tmp/' . $this->image->getFilename());
        }

        $this->redirect(route('dashboard'), navigate: true);
    }
    public function render()
    {
        return view('livewire.task-form');
    }
}
