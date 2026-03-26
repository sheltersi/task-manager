<div class="my-8 mx-4 md:mx-[120px] space-y-6">

    {{-- Stats --}}
    {{-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([['label' => 'Total', 'value' => $this->stats['total']], ['label' => 'In progress', 'value' => $this->stats['in_progress']], ['label' => 'In review', 'value' => $this->stats['in_review']], ['label' => 'Completed', 'value' => $this->stats['completed']],
        ['label' => 'Highest', 'value' => $this->stats['Highest']], ['label' => 'High', 'value' => $this->stats['High']], ['label' => 'Medium', 'value' => $this->stats['Medium']], ['label' => 'Low', 'value' => $this->stats['Low']], ['label' => 'Lowest', 'value' => $this->stats['Lowest']]] as $stat)
            <div
                class="bg-gradient-to-br from-white to-gray-50 border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition">

                <div class="text-xs uppercase tracking-wider text-gray-400 mb-2">
                    {{ $stat['label'] }}
                </div>

                <div class="text-3xl font-semibold tracking-tight text-gray-900">
                    {{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}
                </div>

            </div>
        @endforeach
    </div> --}}
    {{-- Stats --}}
<div class="space-y-4">

    {{-- Status Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach ([
            ['label' => 'Total',       'value' => $this->stats['total'],       'icon' => '󰃤', 'color' => 'text-gray-700'],
            ['label' => 'In Progress', 'value' => $this->stats['in_progress'],  'icon' => '⟳',  'color' => 'text-blue-600'],
            ['label' => 'In Review',   'value' => $this->stats['in_review'],    'icon' => '◎',  'color' => 'text-violet-600'],
            ['label' => 'Completed',   'value' => $this->stats['completed'],    'icon' => '✓',  'color' => 'text-emerald-600'],
        ] as $stat)
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="text-xs uppercase tracking-widest text-gray-400 mb-3">{{ $stat['label'] }}</div>
                <div class="text-3xl font-semibold tracking-tight {{ $stat['color'] }}">
                    {{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Priority Stats --}}
    <div class="grid grid-cols-5 gap-3">
        @foreach ([
            ['label' => 'Highest', 'value' => $this->stats['Highest'], 'color' => 'text-red-600',    'bg' => 'bg-red-50',    'border' => 'border-red-100'],
            ['label' => 'High',    'value' => $this->stats['High'],    'color' => 'text-orange-600', 'bg' => 'bg-orange-50', 'border' => 'border-orange-100'],
            ['label' => 'Medium',  'value' => $this->stats['Medium'],  'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-100'],
            ['label' => 'Low',     'value' => $this->stats['Low'],     'color' => 'text-sky-600',    'bg' => 'bg-sky-50',    'border' => 'border-sky-100'],
            ['label' => 'Lowest',  'value' => $this->stats['Lowest'],  'color' => 'text-gray-500',   'bg' => 'bg-gray-50',   'border' => 'border-gray-100'],
        ] as $stat)
            <div class="{{ $stat['bg'] }} border {{ $stat['border'] }} rounded-2xl p-4 hover:shadow-sm transition-shadow">
                <div class="text-xs uppercase tracking-widest text-gray-400 mb-3">{{ $stat['label'] }}</div>
                <div class="text-3xl font-semibold tracking-tight {{ $stat['color'] }}">
                    {{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        @endforeach
    </div>

</div>

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

        <a href="{{ route('tasks.create') }}" wire:navigate
            class="h-[40px] bg-black text-white rounded-xl px-5 text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition shadow-sm">
            + New Task
        </a>
    </div>


      {{-- Table (owned by TaskTable, only this part re-renders on delete) --}}
    <livewire:task-table
        :search="$search"
        :status="$status"
        :sort="$sort"
        :key="'task-table'"
    />

    </div>
