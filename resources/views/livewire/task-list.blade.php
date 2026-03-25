<div class="my-6 mx-4 md:mx-[100px]">
<div>
    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-2.5 mb-6">
        @foreach([
            ['label' => 'Total',       'value' => $this->stats['total']],
            ['label' => 'In progress', 'value' => $this->stats['in_progress']],
            ['label' => 'In review',   'value' => $this->stats['in_review']],
            ['label' => 'Completed',   'value' => $this->stats['completed']],
        ] as $stat)
        <div class="bg-white border border-black/[0.07] rounded-xl p-4">
            <div class="text-[11px] uppercase tracking-wide text-gray-400 mb-1">{{ $stat['label'] }}</div>
            <div class="text-[22px] font-medium font-mono tracking-tight text-gray-900">{{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}</div>
        </div>
        @endforeach
    </div>

    {{-- Toolbar --}}
    <div class="sticky top-0 z-10 bg-white/80 backdrop-blur border border-black/[0.06] rounded-xl p-3 shadow-sm flex flex-wrap items-center gap-2 mb-4">

        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="🔍 Search tasks..."
            class="flex-1 min-w-[180px] h-[38px] bg-gray-50 border border-black/[0.06] rounded-lg px-3 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-200"
        />

        <select
            wire:model.live="status"
            class="h-[38px] bg-gray-50 border border-black/[0.06] rounded-lg px-3 text-sm text-gray-600 outline-none"
        >
            <option value="">All statuses</option>
            <option value="to_do">To do</option>
            <option value="in_progress">In progress</option>
            <option value="in_review">In review</option>
            <option value="completed">Completed</option>
        </select>

        <select
            wire:model.live="sort"
            class="h-[38px] bg-gray-50 border border-black/[0.06] rounded-lg px-3 text-sm text-gray-600 outline-none"
        >
            <option value="latest">Latest</option>
            <option value="due_date">Due date</option>
            <option value="priority">Priority</option>
        </select>

        <a
            href="{{ route('tasks.create') }}"
            wire:navigate
            class="h-[38px] bg-black text-white rounded-lg px-4 text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition"
        >
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
            <div class="group bg-white border border-black/[0.06] rounded-xl p-4 hover:shadow-md transition-all">

                <div class="flex items-center justify-between gap-4">

                    {{-- Left --}}
                    <div class="space-y-1">
                        <a href="{{ route('tasks.show', $task) }}"
                           wire:navigate
                           class="text-sm font-semibold text-gray-900 group-hover:underline">
                            {{ $task->title }}
                        </a>

                        <div class="text-xs text-gray-400 flex items-center gap-2">
                            <span>•</span>
                            <span>
                                {{ $task->due_date ? 'Due '.$task->due_date->format('M j') : 'No due date' }}
                            </span>
                            <span>•</span>
                            <span>{{ $task->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Right --}}
                    <div class="flex items-center gap-6">

                        {{-- Status --}}
                        <div>
                            <livewire:task-status-select :task="$task" :key="'status-'.$task->id" />
                        </div>
                        {{-- status --}}
                        <span>
                            @switch($task->status)
                                @case('to_do')
                                    <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-md text-xs font-medium">To do</span>
                                    @break
                                @case('in_progress')
                                    <span class="bg-[#E6F1FB] text-[#0C447C] px-2 py-1 rounded-md text-xs font-medium">In progress</span>
                                    @break
                                @case('in_review')
                                    <span class="bg-[#EEEDFE] text-[#3C3489] px-2 py-1 rounded-md text-xs font-medium">In review</span>
                                    @break
                                @case('completed')
                                    <span class="bg-[#EAF3DE] text-[#27500A] px-2 py-1 rounded-md text-xs font-medium">Completed</span>
                                    @break
                                @default
                                    <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-md text-xs font-medium">Unknown</span>
                            @endswitch
                        </span>
                        {{-- Priority Badge --}}
                        <span class="text-xs font-semibold px-2 py-1 rounded-md
                            {{ $task->priority == 1 ? 'bg-red-100 text-red-600' : '' }}
                            {{ $task->priority == 2 ? 'bg-yellow-100 text-yellow-600' : '' }}
                            {{ $task->priority == 3 ? 'bg-green-100 text-green-600' : '' }}">
                            P{{ $task->priority }}
                        </span>

                        {{-- Actions --}}
                        <div class="flex items-center gap-12 opacity-70 group-hover:opacity-100 transition">
                            <a href="{{ route('tasks.show', $task) }}"
                               wire:navigate
                               class="flex items-center justify-center rounded-lg hover:bg-green-100 text-gray-500 hover:text-green-500">
                               <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                viewBox="0 0 576 512"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="green" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6-46.8 43.5-78.1 95.4-93 131.1-3.3 7.9-3.3 16.7 0 24.6 14.9 35.7 46.2 87.7 93 131.1 47.1 43.7 111.8 80.6 192.6 80.6s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1 3.3-7.9 3.3-16.7 0-24.6-14.9-35.7-46.2-87.7-93-131.1-47.1-43.7-111.8-80.6-192.6-80.6zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64-11.5 0-22.3-3-31.7-8.4-1 10.9-.1 22.1 2.9 33.2 13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-12.2-45.7-55.5-74.8-101.1-70.8 5.3 9.3 8.4 20.1 8
                               <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                viewBox="0 0 576 512"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="green" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6-46.8 43.5-78.1 95.4-93 131.1-3.3 7.9-3.3 16.7 0 24.6 14.9 35.7 46.2 87.7 93 131.1 47.1 43.7 111.8 80.6 192.6 80.6s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1 3.3-7.9 3.3-16.7 0-24.6-14.9-35.7-46.2-87.7-93-131.1-47.1-43.7-111.8-80.6-192.6-80.6zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64-11.5 0-22.3-3-31.7-8.4-1 10.9-.1 22.1 2.9 33.2 13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-12.2-45.7-55.5-74.8-101.1-70.8 5.3 9.3 8.4 20.1 8.4 31.7z"/></svg>
                            View</span>
                            </a>
                            <a href="{{ route('tasks.edit', $task) }}"
                               wire:navigate
                               class=" flex items-center justify-center rounded-lg hover:bg-orange-100 text-gray-500 hover:text-orange-500">
                               <span class="flex items-center gap-1">
                               <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                viewBox="0 0 512 512"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="orange" d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152L0 424c0 48.6 39.4 88 88 88l272 0c48.6 0 88-39.4 88-88l0-112c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 112c0 22.1-17.9 40-40 40L88 464c-22.1 0-40-17.9-40-40l0-272c0-22.1 17.9-40 40-40l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24L88 64z"/></svg>
                            Edit</span>
                            </a>

                            <button
                                wire:click="delete({{ $task->id }})"
                                wire:confirm="Delete '{{ addslashes($task->title) }}'?"
                                class=" flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500">
                                <span class="flex items-center gap-1 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 "
                                viewBox="0 0 448 512"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="red" d="M136.7 5.9C141.1-7.2 153.3-16 167.1-16l113.9 0c13.8 0 26 8.8 30.4 21.9L320 32 416 32c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 8.7-26.1zM32 144l384 0 0 304c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-304zm88 64c-13.3 0-24 10.7-24 24l0 192c0 13.3 10.7 24 24 24s24-10.7 24-24l0-192c0-13.3-10.7-24-24-24zm104 0c-13.3 0-24 10.7-24 24l0 192c0 13.3 10.7 24 24 24s24-10.7 24-24l0-192c0-13.3-10.7-24-24-24zm104 0c-13.3 0-24 10.7-24 24l0 192c0 13.3 10.7 24 24 24s24-10.7 24-24l0-192c0-13.3-10.7-24-24-24z"/></svg>
                            Delete</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        @empty
            <div class="text-center py-16 bg-white border border-dashed border-gray-200 rounded-xl">
                <p class="text-gray-500 text-sm mb-2">No tasks yet</p>
                <a href="{{ route('tasks.create') }}"
                   wire:navigate
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
