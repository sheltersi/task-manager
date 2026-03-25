<div class="my-8 mx-4 md:mx-[120px] space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([['label' => 'Total', 'value' => $this->stats['total']], ['label' => 'In progress', 'value' => $this->stats['in_progress']], ['label' => 'In review', 'value' => $this->stats['in_review']], ['label' => 'Completed', 'value' => $this->stats['completed']]] as $stat)
            <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition">

                <div class="text-xs uppercase tracking-wider text-gray-400 mb-2">
                    {{ $stat['label'] }}
                </div>

                <div class="text-3xl font-semibold tracking-tight text-gray-900">
                    {{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}
                </div>

            </div>
        @endforeach
    </div>

    {{-- Toolbar --}}
    <div class="sticky top-0 z-20 bg-white/90 backdrop-blur border border-gray-100 rounded-2xl px-4 py-3 shadow-sm flex flex-wrap items-center gap-3">

        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Search tasks..."
            class="flex-1 min-w-[200px] h-[40px] bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm focus:ring-2 focus:ring-black/10 outline-none" />

        <select wire:model.live="status"
            class="h-[40px] bg-gray-50 border border-gray-200 rounded-xl px-3 text-sm">
            <option value="">All statuses</option>
            <option value="to_do">To do</option>
            <option value="in_progress">In progress</option>
            <option value="in_review">In review</option>
            <option value="completed">Completed</option>
        </select>

        <select wire:model.live="sort"
            class="h-[40px] bg-gray-50 border border-gray-200 rounded-xl px-3 text-sm">
            <option value="latest">Latest</option>
            <option value="due_date">Due date</option>
            <option value="priority">Priority</option>
        </select>

        <a href="{{ route('tasks.create') }}" wire:navigate
            class="h-[40px] bg-black text-white rounded-xl px-5 text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition shadow-sm">
            + New Task
        </a>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Task List --}}
    <div class="space-y-3">

        @forelse ($tasks as $task)
            <div class="group bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-lg hover:-translate-y-[2px] transition-all">

                <div class="flex items-center justify-between gap-6">

                    {{-- Left --}}
                    <div class="space-y-2">
                        <a href="{{ route('tasks.show', $task) }}" wire:navigate
                            class="text-base font-semibold text-gray-900 group-hover:text-black">
                            {{ $task->title }}
                        </a>

                        <div class="text-xs text-gray-400 flex items-center gap-3">
                            <span>
                                {{ $task->due_date ? 'Due ' . $task->due_date->format('M j') : 'No due date' }}
                            </span>
                            <span>•</span>
                            <span>{{ $task->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Right --}}
                    <div class="flex items-center gap-4">

                        {{-- Status Select --}}
                        <livewire:task-status-select :task="$task" :key="'status-' . $task->id" />

                        {{-- Priority --}}
                        <livewire:task-priority-select :task="$task" :key="'priority-' . $task->id" />

                        {{-- Status Badge --}}
                        @switch($task->status)
                            @case('to_do')
                                <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600">To do</span>
                            @break

                            @case('in_progress')
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">In progress</span>
                            @break

                            @case('in_review')
                                <span class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-700">In review</span>
                            @break

                            @case('completed')
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">Completed</span>
                            @break
                        @endswitch

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">

                            <a href="{{ route('tasks.show', $task) }}" wire:navigate
                                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-green-600">
                                👁️
                            </a>

                            <a href="{{ route('tasks.edit', $task) }}" wire:navigate
                                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-orange-600">
                                ✏️
                            </a>

                            <button wire:click="delete({{ $task->id }})"
                                class="p-2 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500">
                                🗑️
                            </button>

                        </div>

                    </div>
                </div>
            </div>

        @empty
            <div class="text-center py-16 bg-white border border-dashed border-gray-200 rounded-2xl">
                <p class="text-gray-500 text-sm mb-2">No tasks yet</p>
                <a href="{{ route('tasks.create') }}" wire:navigate
                    class="text-sm text-black font-medium underline underline-offset-2">
                    Create your first task →
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($tasks->hasPages())
        <div class="flex justify-end pt-2">
            {{ $tasks->links('livewire::tailwind') }}
        </div>
    @endif

</div>
