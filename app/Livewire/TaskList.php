<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sort = 'latest';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function render()
    {
          $query = auth()->user()->tasks();

        if ($this->search) {
            $query->where(fn($q) => $q
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

        return view('livewire.task-list',[
            'tasks' => $query->paginate(10),
        ]);
    }
}
