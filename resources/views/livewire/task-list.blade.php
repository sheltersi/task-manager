<div>
    {{-- Toolbar --}}
    <div class="flex items-center gap-2 mb-4">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Search tasks…"
            class="flex-1 h-[34px] bg-white border border-black/[0.07] rounded-lg px-3 text-[13px] text-gray-800 placeholder-gray-400 outline-none focus:border-black/20"
        />
        <select
            wire:model.live="status"
            class="h-[34px] bg-white border border-black/[0.07] rounded-lg px-3 text-[13px] text-gray-500 outline-none min-w-[130px]"
        >
            <option value="">All statuses</option>
            <option value="to_do">To do</option>
            <option value="in_progress">In progress</option>
            <option value="in_review">In review</option>
            <option value="completed">Completed</option>
        </select>
        <select
            wire:model.live="sort"
            class="h-[34px] bg-white border border-black/[0.07] rounded-lg px-3 text-[13px] text-gray-500 outline-none min-w-[130px]"
        >
            <option value="latest">Latest first</option>
            <option value="due_date">By due date</option>
            <option value="priority">By priority</option>
        </select>
<a
            href="{{ route('tasks.create') }}"
            wire:navigate
            class="h-[34px] bg-gray-900 text-white rounded-lg px-4 text-[13px] font-medium flex items-center gap-1 hover:bg-gray-800 transition-colors"
        >
            + New task
        </a>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-2.5 bg-[#EAF3DE] border border-[#97C459]/40 rounded-lg text-[13px] text-[#27500A]">
            {{ session('success') }}
        </div>
    @endif

    {{-- Task rows --}}
    <div class="bg-white border border-black/[0.07] rounded-xl overflow-hidden divide-y divide-black/[0.05]">
        @forelse ($tasks as $task)
            <div class="grid items-center gap-4 px-4 py-3 hover:bg-[#F7F6F2] transition-colors"
                 style="grid-template-columns: 1fr auto auto auto">

                {{-- Title + meta --}}
                <div>
                    <a href="{{ route('tasks.show', $task) }}" wire:navigate
                       class="text-[13px] font-medium text-gray-900 hover:underline underline-offset-2">
                        {{ $task->title }}
                    </a>
                    <div class="text-[11px] text-gray-400 mt-0.5">
                        {{ $task->due_date ? 'Due '.$task->due_date->format('M j') : 'No due date' }}
                        · created {{ $task->created_at->diffForHumans() }}
                    </div>
                </div>

                {{-- Inline status --}}
                <livewire:task-status-select :task="$task" :key="'status-'.$task->id" />

                {{-- Priority --}}
                <span class="font-mono text-[11px] text-gray-400 w-7 text-right">
                    P{{ $task->priority }}
                </span>

                {{-- Actions --}}
                <div class="flex items-center gap-1">
                    <a href="{{ route('tasks.edit', $task) }}"
                       wire:navigate
                       class="w-[26px] h-[26px] flex items-center justify-center border border-black/[0.07] rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-colors text-[13px]">
                        ✎
                    </a>
                    <button
                        wire:click="delete({{ $task->id }})"
                        wire:confirm="Delete '{{ addslashes($task->title) }}'?"
                        class="w-[26px] h-[26px] flex items-center justify-center border border-black/[0.07] rounded-md text-gray-400 hover:text-red-500 hover:border-red-200 transition-colors text-[13px]">
                        ✕
                    </button>
                </div>
            </div>
        @empty
            <div class="px-4 py-10 text-center text-[13px] text-gray-400">
                No tasks found. <a href="{{ route('tasks.create') }}" wire:navigate class="text-gray-700 underline underline-offset-2">Create one?</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($tasks->hasPages())
        <div class="mt-3 flex justify-end">
            {{ $tasks->links('livewire::tailwind') }}
        </div>
    @endif
</div>
