<div>
    <h4 class="text-[13px] font-medium text-gray-900 mb-3">Subtasks</h4>

    {{-- Add subtask form --}}
    <div class="flex gap-2 mb-3">
        <input
            wire:model="title"
            wire:keydown.enter="addSubtask"
            type="text"
            placeholder="Add a subtask…"
            class="flex-1 h-[32px] border border-black/[0.08] rounded-lg px-3 text-[13px] text-gray-900 outline-none focus:border-black/25 placeholder-gray-300"
        >
        <button
            wire:click="addSubtask"
            wire:loading.attr="disabled"
            class="h-[32px] px-3 bg-gray-900 text-white rounded-lg text-[12px] font-medium hover:bg-gray-800 disabled:opacity-50 transition-colors">
            <span wire:loading.remove>Add</span>
            <span wire:loading>…</span>
        </button>
    </div>
    @error('title')
        <p class="text-[12px] text-red-500 mb-2">{{ $message }}</p>
    @enderror

    {{-- Subtask list --}}
    <div class="space-y-1.5">
        @forelse($subtasks as $subtask)
            <div class="flex items-center gap-2 group">
                <button
                    wire:click="toggleSubtask({{ $subtask->id }})"
                    class="shrink-0 w-4 h-4 rounded border {{ $subtask->is_completed ? 'bg-gray-900 border-gray-900' : 'border-gray-300 hover:border-gray-400' }} flex items-center justify-center transition-colors">
                    @if($subtask->is_completed)
                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </button>
                <span class="flex-1 text-[13px] {{ $subtask->is_completed ? 'text-gray-400 line-through' : 'text-gray-700' }}">
                    {{ $subtask->title }}
                </span>
                <button
                    wire:click="deleteSubtask({{ $subtask->id }})"
                    wire:confirm="Delete this subtask?"
                    class="shrink-0 w-5 h-5 rounded text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @empty
            <p class="text-[12px] text-gray-400">No subtasks yet.</p>
        @endforelse
    </div>

    {{-- Progress --}}
    @if($subtasks->count() > 0)
        <div class="mt-3 pt-3 border-t border-black/[0.06]">
            @php
                $completed = $subtasks->where('is_completed', true)->count();
                $total = $subtasks->count();
                $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp
            <div class="flex items-center justify-between text-[11px] text-gray-400 mb-1.5">
                <span>{{ $completed }}/{{ $total }} completed</span>
                <span>{{ $percentage }}%</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gray-900 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    @endif
</div>
