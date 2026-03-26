<div>
    @if($this->sharedTasks->count() > 0)
        <div class="mt-8">
            <h2 class="text-[13px] font-medium text-gray-500 uppercase tracking-wide mb-3">
                Shared with me
            </h2>
            <div class="bg-white border border-black/[0.07] rounded-xl overflow-hidden divide-y divide-black/[0.05]">
                @foreach($this->sharedTasks as $task)
                    <div class="flex items-center gap-4 px-4 py-3 hover:bg-[#F7F6F2] transition-colors">

                        {{-- Title + owner --}}
                        <div class="flex-1">
                            <a href="{{ route('tasks.show', $task) }}" wire:navigate
                               class="text-[13px] font-medium text-gray-900 hover:underline underline-offset-2">
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
                            'inline-flex items-center h-[20px] px-2 rounded-md text-[10px] font-medium',
                            'bg-[#E6F1FB] text-[#0C447C]' => $role === 'commenter',
                            'bg-gray-100 text-gray-500'   => $role === 'viewer',
                        ])>
                            {{ ucfirst($role) }}
                        </span>

                        {{-- Status --}}
                        @php
                            $statusMap = [
                                'to_do'       => ['label' => 'To do',       'class' => 'bg-gray-100 text-gray-500'],
                                'in_progress' => ['label' => 'In progress', 'class' => 'bg-[#E6F1FB] text-[#0C447C]'],
                                'in_review'   => ['label' => 'In review',   'class' => 'bg-[#EEEDFE] text-[#3C3489]'],
                                'completed'   => ['label' => 'Completed',   'class' => 'bg-[#EAF3DE] text-[#27500A]'],
                            ];
                            $s = $statusMap[$task->status];
                        @endphp
                        <span class="inline-flex items-center h-[20px] px-2 rounded-md text-[10px] font-medium {{ $s['class'] }}">
                            {{ $s['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
