<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskShare;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TaskShareManager extends Component
{
    public Task $task;
    public bool $showModal = false;

    #[Validate('required|email|exists:users,email')]
    public string $email = '';

    #[Validate('required|in:viewer,commenter')]
    public string $role = 'commenter';

     public function mount(Task $task): void
    {
        $this->task = $task;
    }

    public function openModal(): void
    {
        Gate::authorize('share', $this->task);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal  = false;
        $this->email      = '';
        $this->role       = 'commenter';
        $this->resetErrorBag(); // clears validation errors
    }

      public function invite(): void
    {
        Gate::authorize('share', $this->task);

        $this->validate();

        $user = User::where('email', $this->email)->first();

        // Prevent owner sharing with themselves
        if ($user->id === $this->task->user_id) {
            $this->addError('email', 'You cannot share a task with yourself.');
            return;
        }
        // Update role if already shared, otherwise create
        TaskShare::updateOrCreate(
            ['task_id' => $this->task->id, 'user_id' => $user->id],
            ['role'    => $this->role]
        );

        $this->email = '';
        $this->role  = 'commenter';
        unset($this->collaborators); // refresh computed list
    }
  public function removeCollaborator(int $userId): void
    {
        Gate::authorize('share', $this->task);

        TaskShare::where('task_id', $this->task->id)
            ->where('user_id', $userId)
            ->delete();

        unset($this->collaborators);
    }

    public function updateRole(int $userId, string $role): void
    {
        Gate::authorize('share', $this->task);

        TaskShare::where('task_id', $this->task->id)
            ->where('user_id', $userId)
            ->update(['role' => $role]);

        unset($this->collaborators);
    }

    #[Computed]
    public function collaborators()
    {
        return $this->task->shares()->with('user')->get();
    }

    public function render()
    {
        return view('livewire.task-share-manager');
    }
}
