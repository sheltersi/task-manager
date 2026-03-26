<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CommentForm extends Component
{
    use WithFileUploads;

    public Task $task;

    #[Validate('required|string|min:3|max:1000')]
    public string $body = '';

    #[Validate('nullable|image|max:2048')]
    public $image = null;

    public ?string $imagePreview = null;

    public function mount(Task $task): void
    {
        $this->task = $task;
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

    public function submit(): void
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('comment-images', 'public');
        }

        $this->task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->body,
            'image_path' => $imagePath,
        ]);

        $this->body = '';
        $this->image = null;
        $this->imagePreview = null;

        $this->dispatch('comment-added');
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
