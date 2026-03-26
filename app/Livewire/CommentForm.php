<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CommentForm extends Component
{
       public Task $task;

    #[Validate('required|string|min:3|max:1000')]
    public string $body = '';

    public function mount(Task $task): void
    {
        $this->task = $task;
    }

    public function submit(): void
    {
        $this->validate();

        $this->task->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $this->body,
        ]);

        $this->body = ''; // clear the textarea after submitting

        $this->dispatch('comment-added');
        // TaskComments hears this and refreshes the list
    }
    public function render()
    {
        return view('livewire.comment-form');
    }
}
