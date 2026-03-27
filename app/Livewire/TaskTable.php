<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TaskTable extends Component
{
    use WithPagination;
    public ?int $deletingTaskId = null;

    // Received from the parent TaskList component
    // These are passed in like: <livewire:task-table :search="$search" ... />
    public string $search = '';
    public string $status = '';
    public string $sort = 'latest';



    // Listen for the parent telling us to reset the page
    // e.g. when filters change in TaskList
    #[On('filters-updated')]
    public function handleFiltersUpdated(string $search, string $status, string $sort): void
    {
        $this->search = $search;
        $this->status = $status;
        $this->sort   = $sort;
        $this->resetPage();
    }

    // Listen for a targeted refresh — only this component re-renders
    #[On('refresh-task-table')]
    #[On('task-status-updated')]
    public function refresh(): void
    {
        // Empty method body is enough
        // Just receiving the event causes this component to re-render
    }

     public function confirmDelete(int $id): void
    {
        $this->deletingTaskId = $id;
    }

    public function delete(): void
    {
        $task = Task::findOrFail($this->deletingTaskId);
        Gate::authorize('delete', $task);
        $task->delete();

        $this->deletingTaskId = null;
        // Tell the parent to update stats without re-rendering everything
        $this->dispatch('update-stats');
        $this->dispatch('task-deleted', 'Task deleted successfully.');

        // session()->flash('success', 'Task deleted.');
    }

    public function cancelDelete(): void
{
    $this->deletingTaskId = null;
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

        return view('livewire.task-table', [
            'tasks' => $query->paginate(10),
        ]);
    }
}
