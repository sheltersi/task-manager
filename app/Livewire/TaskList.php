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
        $this->dispatchFilters();
    }
    public function updatedStatus(): void
    {
        $this->dispatchFilters();
    }

     public function updatedSort(): void
    {
        $this->dispatchFilters();
    }

     private function dispatchFilters(): void
    {
        $this->dispatch('filters-updated',
            search: $this->search,
            status: $this->status,
            sort:   $this->sort,
        );
    }

    // Listen for a delete from TaskTable and refresh stats
    #[On('update-stats')]
    public function handleTaskUpdates(): void
    {
        unset($this->stats); // clears computed cache so counts recalculate
    }

      #[On('task-status-updated')]
    public function refreshStats(): void
    {
        // This method will be called whenever the 'task-status-updated' event is dispatched.
        // You can perform any necessary actions here, such as refreshing data or updating the UI.
        // For this example, we don't need to do anything specific, as the stats will be recomputed automatically.
        unset($this->stats); // Clear the cached stats to force recomputation
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
            'Highest' => $user->tasks()->where('priority', '5')->count(),
            'High'   => $user->tasks()->where('priority', '4')->count(),
            'Medium'   => $user->tasks()->where('priority', '3')->count(),
            'Low'   => $user->tasks()->where('priority', '2')->count(),
            'Lowest'   => $user->tasks()->where('priority', '1')->count(),
        ];
    }


    public function render()
    {
       return view('livewire.task-list');
    }
}
