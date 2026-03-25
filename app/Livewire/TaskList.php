<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sort = 'latest';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
{
    $task = Task::findOrFail($id);

    Gate::authorize('delete', $task);

    $task->delete();

    session()->flash('success', 'Task deleted.');
}

    #[Computed]
    public function stats(): array
    {
        $user = auth()->user();

        return [
            'total'       => $user->tasks()->count(),
            'in_progress' => $user->tasks()->where('status', 'in_progress')->count(),
            'in_review'   => $user->tasks()->where('status', 'in_review')->count(),
            'completed'   => $user->tasks()->where('status', 'completed')->count(),
        ];
    }

    #[On('task-status-updated')]
    public function refreshStats(): void
    {
        // This method will be called whenever the 'task-status-updated' event is dispatched.
        // You can perform any necessary actions here, such as refreshing data or updating the UI.
        // For this example, we don't need to do anything specific, as the stats will be recomputed automatically.
        unset($this->stats); // Clear the cached stats to force recomputation
    }
    public function render()
    {
        $query = auth()->user()->tasks();

        if ($this->search) {
            $query->where(
                fn($q) => $q
                    ->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
            );
        }

        $allowed = ['to_do', 'in_progress', 'in_review', 'completed'];
        if (in_array($this->status, $allowed)) {
            $query->where('status', $this->status);
        }

        match ($this->sort) {
            'due_date' => $query->orderByRaw('due_date IS NULL')->orderBy('due_date'),
            'priority' => $query->orderBy('priority', 'desc'),
            default    => $query->latest(),
        };

        return view('livewire.task-list', [
            'tasks' => $query->paginate(10),
        ]);
    }
}
