@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div>
    {{-- Comment count heading --}}
    <h3 class="text-[13px] font-medium text-gray-900 mb-4">
        Comments
        @if($this->comments->count() > 0)
            <span class="ml-1.5 text-[11px] font-normal text-gray-400">
                {{ $this->comments->count() }}
            </span>
        @endif
    </h3>

    {{-- Comment form --}}
    <livewire:comment-form :task="$task" :key="'comment-form-'.$task->id" />

    {{-- Comment list --}}
    <div class="mt-6 flex flex-col gap-4">
        @forelse($this->comments as $comment)
            <div class="flex gap-3">

                {{-- Avatar --}}
                <div class="w-7 h-7 shrink-0 rounded-full bg-gray-100 border border-black/[0.07] flex items-center justify-center text-[11px] font-medium text-gray-500">
                    {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                </div>

                {{-- Body --}}
                <div class="flex-1">
                    <div class="flex items-baseline justify-between gap-2 mb-1">
                        <span class="text-[13px] font-medium text-gray-900">
                            {{ $comment->user->name }}
                        </span>
                        <span class="text-[11px] text-gray-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-[13px] text-gray-600 leading-relaxed">
                        {{ $comment->body }}
                    </p>

                    {{-- Image display --}}
                    @if($comment->image_path)
                        <div class="mt-2">
                            <img
                                src="{{ Storage::url($comment->image_path) }}"
                                alt="Comment image"
                                class="h-32 w-32 object-cover rounded-lg border border-black/[0.08] cursor-pointer hover:opacity-90 transition-opacity"
                                onclick="window.open('{{ Storage::url($comment->image_path) }}', '_blank')"
                            >
                        </div>
                    @endif

                    {{-- Delete — only visible to comment author --}}
                    @if($comment->user_id === auth()->id())
                        <button
                            wire:click="delete({{ $comment->id }})"
                            wire:confirm="Delete this comment?"
                            class="mt-1 text-[11px] text-gray-400 hover:text-red-500 transition-colors">
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-[13px] text-gray-400">No comments yet. Be the first to comment.</p>
        @endforelse
    </div>
</div>
