<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class SharedTaskList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sort   = 'latest';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
        public function updatedSort(): void   { $this->resetPage(); }

     #[Computed]
    public function sharedTasks()
    {
         $query = Task::whereHas('shares', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['user', 'shares' => function ($q) {
                $q->where('user_id', auth()->id());
            }]);

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

        return $query->paginate(10);
    }

 #[Computed]
    public function totalShared(): int
    {
        return Task::whereHas('shares', fn($q) =>
            $q->where('user_id', auth()->id())
        )->count();
    }

    public function render()
    {
        return view('livewire.shared-task-list');
    }

}
