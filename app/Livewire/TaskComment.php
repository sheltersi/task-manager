<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskComment extends Component
{
      public Task $task;

    public function mount(Task $task): void
    {
        $this->task = $task;
    }

    // When CommentForm dispatches 'comment-added',
    // this component re-renders with fresh comments
    #[On('comment-added')]
    public function refresh(): void
    {
        unset($this->comments);
    }

    #[Computed]
    public function comments()
    {
        return $this->task->comments()->with('user')->get();
        // with('user') eager loads the commenter's name
        // avoids N+1 — one query for all comments + one for all users
    }

    public function delete(int $id): void
    {
        $comment = \App\Models\Comment::findOrFail($id);

        // Only the comment author can delete their own comment
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();
        unset($this->comments);
    }
    public function render()
    {
        return view('livewire.task-comment');
    }
}
