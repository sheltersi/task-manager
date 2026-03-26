<div>
    <div class="flex gap-3">

        {{-- Current user avatar --}}
        <div class="w-7 h-7 shrink-0 rounded-full bg-gray-100 border border-black/[0.07] flex items-center justify-center text-[11px] font-medium text-gray-500">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>

        {{-- Input area --}}
        <div class="flex-1">
            <textarea
                wire:model="body"
                wire:keydown.meta.enter="submit"
                rows="2"
                placeholder="Write a comment…"
                class="w-full border border-black/[0.08] rounded-lg px-3 py-2.5 text-[13px] text-gray-900 outline-none focus:border-black/25 placeholder-gray-300 resize-none leading-relaxed"
            ></textarea>

            @error('body')
                <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-between mt-2">
                <span class="text-[11px] text-gray-400">
                    ⌘ + Enter to submit
                </span>
                <button
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    class="h-[30px] px-4 bg-gray-900 text-white rounded-lg text-[12px] font-medium hover:bg-gray-800 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="submit">Comment</span>
                    <span wire:loading wire:target="submit">Posting…</span>
                </button>
            </div>
        </div>
    </div>
</div>
