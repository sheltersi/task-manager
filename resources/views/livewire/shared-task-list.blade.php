<div>
    @if($this->totalShared > 0)
        <div class="mt-8">

            {{-- Section header --}}
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <h2 class="text-[13px] font-medium text-gray-500 uppercase tracking-wide">
                        Shared with me
                    </h2>
                    <span class="inline-flex items-center justify-center h-[18px] px-2 rounded-full bg-gray-100 text-[11px] font-medium text-gray-500">
                        {{ $this->totalShared }}
                    </span>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="flex items-center gap-2 mb-3">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search shared tasks…"
                    class="flex-1 h-[32px] bg-white border border-black/[0.07] rounded-lg px-3 text-[13px] text-gray-800 placeholder-gray-400 outline-none focus:border-black/20"
                />
                <select
                    wire:model.live="status"
                    class="h-[32px] bg-white border border-black/[0.07] rounded-lg px-3 text-[13px] text-gray-500 outline-none min-w-[120px]">
                    <option value="">All statuses</option>
                    <option value="to_do">To do</option>
                    <option value="in_progress">In progress</option>
                    <option value="in_review">In review</option>
                    <option value="completed">Completed</option>
                </select>
                <select
                    wire:model.live="sort"
                    class="h-[32px] bg-white border border-black/[0.07] rounded-lg px-3 text-[13px] text-gray-500 outline-none min-w-[120px]">
                    <option value="latest">Latest first</option>
                    <option value="due_date">By due date</option>
                    <option value="priority">By priority</option>
                </select>
            </div>

            {{-- Task rows --}}
            <div class="bg-white border border-black/[0.07] rounded-xl overflow-hidden divide-y divide-black/[0.05]">
                @forelse($this->sharedTasks as $task)
                    <div class="flex items-center gap-4 px-4 py-3 hover:bg-[#F7F6F2] transition-colors">

                        {{-- Title + owner --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('tasks.show', $task) }}" wire:navigate
                               class="text-[13px] font-medium text-gray-900 hover:underline underline-offset-2 truncate block">
                                {{ $task->title }}
                            </a>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                Owned by {{ $task->user->name }}
                                · {{ $task->due_date ? 'Due '.$task->due_date->format('M j') : 'No due date' }}
                            </div>
                        </div>

                        {{-- Role badge --}}
                        @php $role = $task->shares->first()?->role @endphp
                        <span @class([
                            'shrink-0 inline-flex items-center h-[20px] px-2 rounded-md text-[10px] font-medium',
                            'bg-[#E6F1FB] text-[#0C447C]' => $role === 'commenter',
                            'bg-gray-100 text-gray-500'   => $role === 'viewer',
                        ])>
                            {{ ucfirst($role) }}
                        </span>

                        {{-- Status badge --}}
                        @php
                            $statusMap = [
                                'to_do'       => ['label' => 'To do',       'class' => 'bg-gray-100 text-gray-500'],
                                'in_progress' => ['label' => 'In progress', 'class' => 'bg-[#E6F1FB] text-[#0C447C]'],
                                'in_review'   => ['label' => 'In review',   'class' => 'bg-[#EEEDFE] text-[#3C3489]'],
                                'completed'   => ['label' => 'Completed',   'class' => 'bg-[#EAF3DE] text-[#27500A]'],
                            ];
                            $s = $statusMap[$task->status] ?? $statusMap['to_do'];
                        @endphp
                        <span class="shrink-0 inline-flex items-center h-[20px] px-2 rounded-md text-[10px] font-medium {{ $s['class'] }}">
                            {{ $s['label'] }}
                        </span>

                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-[13px] text-gray-400">
                        No shared tasks match your search.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($this->sharedTasks->hasPages())
                <div class="mt-3 flex justify-end">
                    {{ $this->sharedTasks->links('livewire::tailwind') }}
                </div>
            @endif

        </div>
    @endif
</div>
