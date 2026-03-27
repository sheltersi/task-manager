<div class="my-8 mx-4 md:mx-[120px] space-y-6">
    {{-- Stats --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-[16px] font-medium text-gray-900">Task Statistics</h2>
            <div class="flex items-center gap-2">
                <button wire:click="$refresh" class="p-1 rounded-md text-gray-400 hover:text-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 .001h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.183m0-4.991v4.99" />
                    </svg>
                </button>
            </div>
        </div>
        {{-- Status Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ([['label' => 'Total', 'value' => $this->stats['total'], 'accent' => 'bg-gray-400'], ['label' => 'In Progress', 'value' => $this->stats['in_progress'], 'accent' => 'bg-blue-500'], ['label' => 'In Review', 'value' => $this->stats['in_review'], 'accent' => 'bg-violet-500'], ['label' => 'Completed', 'value' => $this->stats['completed'], 'accent' => 'bg-emerald-500']] as $stat)
                <div class="relative bg-gray-50 rounded-xl p-4 overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full {{ $stat['accent'] }} rounded-l-xl"></div>
                    <div class="text-[10px] font-medium uppercase tracking-widest text-gray-400 mb-2 pl-1">
                        {{ $stat['label'] }}
                    </div>
                    <div class="font-['Syne',sans-serif] text-2xl font-bold tracking-tight text-gray-800 pl-1">
                        {{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Priority Stats --}}
        <div class="grid grid-cols-5 gap-3">
            @foreach ([['label' => 'Highest', 'value' => $this->stats['Highest'], 'accent' => 'bg-red-500', 'text' => 'text-red-600'], ['label' => 'High', 'value' => $this->stats['High'], 'accent' => 'bg-orange-500', 'text' => 'text-orange-600'], ['label' => 'Medium', 'value' => $this->stats['Medium'], 'accent' => 'bg-yellow-400', 'text' => 'text-yellow-600'], ['label' => 'Low', 'value' => $this->stats['Low'], 'accent' => 'bg-sky-500', 'text' => 'text-sky-600'], ['label' => 'Lowest', 'value' => $this->stats['Lowest'], 'accent' => 'bg-gray-300', 'text' => 'text-gray-500']] as $stat)
                <div class="relative bg-gray-50 rounded-xl p-4 overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full {{ $stat['accent'] }} rounded-l-xl"></div>
                    <div class="text-[10px] font-medium uppercase tracking-widest text-gray-400 mb-2 pl-1">
                        {{ $stat['label'] }}
                    </div>
                    <div class="font-['Syne',sans-serif] text-2xl font-bold tracking-tight {{ $stat['text'] }} pl-1">
                        {{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- Charts --}}
    <livewire:task-chart :key="'task-chart'" />

    {{-- Toolbar --}}
    <div
        class="sticky top-0 z-20 bg-white/90 backdrop-blur border border-gray-100 rounded-2xl px-4 py-3 shadow-sm flex flex-wrap items-center gap-3">

        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tasks..."
            class="flex-1 min-w-[200px] h-[40px] bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm focus:ring-2 focus:ring-black/10 outline-none" />

        <select wire:model.live="status" class="h-[40px] bg-gray-50 border border-gray-200 rounded-xl px-3 text-sm">
            <option value="">All statuses</option>
            <option value="to_do">To do</option>
            <option value="in_progress">In progress</option>
            <option value="in_review">In review</option>
            <option value="completed">Completed</option>
        </select>

        <select wire:model.live="sort" class="h-[40px] bg-gray-50 border border-gray-200 rounded-xl px-3 text-sm">
            <option value="latest">Latest</option>
            <option value="due_date">Due date</option>
            <option value="priority">Priority</option>
        </select>

        {{-- Shared with me badge --}}
        @if ($this->sharedCount > 0)
            <a href="{{ route('shared.tasks') }}" wire:navigate
                class="relative h-[34px] flex items-center gap-2 px-3 bg-white border border-black/[0.07] rounded-lg text-[13px] text-gray-600 hover:bg-[#F7F6F2] transition-colors">
                Shared with me
                <span
                    class="inline-flex items-center justify-center h-[18px] min-w-[18px] px-1.5 rounded-full bg-[#FAECE7] text-[#993C1D] text-[10px] font-medium">
                    {{ $this->sharedCount }}
                </span>
            </a>
        @endif

        <a href="{{ route('tasks.create') }}" wire:navigate
            class="h-[40px] bg-black text-white rounded-xl px-5 text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition shadow-sm">
            + New Task
        </a>
    </div>

    {{-- Table (owned by TaskTable, only this part re-renders on delete) --}}
    <livewire:task-table :search="$search" :status="$status" :sort="$sort" :key="'task-table'" />

</div>
