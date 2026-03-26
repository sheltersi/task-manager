@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="mx-6 md:mx-[120px] my-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">

            <a href="{{ route('dashboard') }}" wire:navigate
                class="w-[30px] h-[30px] flex items-center justify-center border border-black/[0.07] rounded-lg bg-white text-gray-400 hover:text-gray-700 text-sm transition-colors">
                ←
            </a>
            <h2 class="text-[16px] font-medium text-gray-900">All Tasks</h2>
        </div>
        <div class="flex items-center gap-2">
            {{-- Share manager (owner only, handles its own visibility) --}}
            <livewire:task-share-manager :task="$task" :key="'share-' . $task->id" />

            @if ($task->user_id === auth()->id())
                <a href="{{ route('tasks.edit', $task) }}" wire:navigate
                    class="h-[32px] px-4 border border-black/[0.08] rounded-lg text-[13px] font-medium text-gray-500 hover:text-gray-800 flex items-center transition-colors">
                    Edit
                </a>
                <button wire:click="delete" wire:confirm="Delete this task permanently?"
                    class="h-[32px] px-4 border border-red-200 rounded-lg text-[13px] font-medium text-red-400 hover:text-red-600 flex items-center transition-colors">
                    Delete
                </button>
            @endif

        </div>
    </div>

    {{-- Card --}}
    <div class="bg-white border border-black/[0.07] rounded-xl overflow-hidden">

        {{-- Title block --}}
        <div class="px-6 py-5 border-b border-black/[0.06]">
            <div class="flex items-start justify-between gap-4">
                <h1 class="text-[17px] font-medium text-gray-900 leading-snug">{{ $task->title }}</h1>
                @php
                    $statusMap = [
                        'to_do' => ['label' => 'To do', 'class' => 'bg-gray-100 text-gray-500'],
                        'in_progress' => ['label' => 'In progress', 'class' => 'bg-[#E6F1FB] text-[#0C447C]'],
                        'in_review' => ['label' => 'In review', 'class' => 'bg-[#EEEDFE] text-[#3C3489]'],
                        'completed' => ['label' => 'Completed', 'class' => 'bg-[#EAF3DE] text-[#27500A]'],
                    ];
                    $s = $statusMap[$task->status] ?? $statusMap['to_do'];
                @endphp
                <span
                    class="shrink-0 inline-flex items-center h-[22px] px-2.5 rounded-md text-[11px] font-medium {{ $s['class'] }}">
                    {{ $s['label'] }}
                </span>
            </div>
            @if ($task->description)
                <p class="mt-3 text-[13px] text-gray-500 leading-relaxed">{{ $task->description }}</p>
            @endif

            @if($task->image_path)
                <div class="mt-3">
                    <img
                        src="{{ Storage::url($task->image_path) }}"
                        alt="Task attachment"
                        class="h-48 w-auto rounded-lg border border-black/[0.08] cursor-pointer hover:opacity-90 transition-opacity"
                        onclick="window.open('{{ Storage::url($task->image_path) }}', '_blank')"
                    >
                </div>
            @endif
        </div>

        {{-- Meta grid --}}
        <div class="grid grid-cols-3 divide-x divide-black/[0.05]">
            <div class="px-6 py-4">
                <div class="text-[11px] uppercase tracking-wide text-gray-400 mb-1">Priority</div>
                <div class="flex items-center gap-1.5">
                    @for ($i = 1; $i <= 5; $i++)
                        <span @class([
                            'w-5 h-5 rounded flex items-center justify-center text-[10px] font-mono font-medium',
                            'bg-gray-900 text-white' => $i === $task->priority,
                            'bg-gray-100 text-gray-300' => $i !== $task->priority,
                        ])>{{ $i }}</span>
                    @endfor
                </div>
            </div>
            <div class="px-6 py-4">
                <div class="text-[11px] uppercase tracking-wide text-gray-400 mb-1">Due date</div>
                <div class="text-[13px] font-medium text-gray-900">
                    @if ($task->due_date)
                        <span @class([
                            'text-red-500' =>
                                $task->due_date->isPast() && $task->status !== 'completed',
                        ])>
                            {{ $task->due_date->format('M j, Y') }}
                        </span>
                    @else
                        <span class="text-gray-400">No due date</span>
                    @endif
                </div>
            </div>
            <div class="px-6 py-4">
                <div class="text-[11px] uppercase tracking-wide text-gray-400 mb-1">Created</div>
                <div class="text-[13px] font-medium text-gray-900">{{ $task->created_at->format('M j, Y') }}</div>
                <div class="text-[11px] text-gray-400">{{ $task->created_at->diffForHumans() }}</div>
            </div>
        </div>

        {{-- Subtasks --}}
        <div class="px-6 py-4 border-t border-black/[0.06]">
            <livewire:subtask-form :task="$task" :key="'subtasks-' . $task->id" />
        </div>

        {{-- Inline status change --}}
        <div class="px-6 py-4 border-t border-black/[0.06] bg-[#F7F6F2]/60">
            @if ($task->user_id === auth()->id())
                <livewire:task-status-select :task="$task" :key="'show-status-' . $task->id" />
            @else
                {{-- Collaborators see the status as read-only --}}
                @php
                    $statusMap = [
                        'to_do' => ['label' => 'To do', 'class' => 'bg-gray-100 text-gray-500'],
                        'in_progress' => ['label' => 'In progress', 'class' => 'bg-[#E6F1FB] text-[#0C447C]'],
                        'in_review' => ['label' => 'In review', 'class' => 'bg-[#EEEDFE] text-[#3C3489]'],
                        'completed' => ['label' => 'Completed', 'class' => 'bg-[#EAF3DE] text-[#27500A]'],
                    ];
                    $s = $statusMap[$task->status] ?? $statusMap['to_do'];
                @endphp
                <span
                    class="inline-flex items-center h-[22px] px-2.5 rounded-md text-[11px] font-medium {{ $s['class'] }}">
                    {{ $s['label'] }}
                </span>
            @endif
        </div>
    </div>
    {{-- Comments section --}}
    <div class="mt-6 bg-white border border-black/[0.07] rounded-xl p-6">
        <livewire:task-comment :task="$task" :key="'comments-' . $task->id" />
    </div>
</div>
