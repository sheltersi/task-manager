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

            {{-- Image preview --}}
            @if($imagePreview)
                <div class="mt-2 relative inline-block">
                    <img src="{{ $imagePreview }}" alt="Image preview" class="h-24 w-auto rounded-lg border border-black/[0.08]">
                    <button
                        type="button"
                        wire:click="removeImage"
                        class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-[10px] hover:bg-red-600">
                        ×
                    </button>
                </div>
            @endif

            {{-- Image upload button --}}
            @if(!$imagePreview)
                <div class="mt-2">
                    <label class="inline-flex items-center gap-1.5 text-[12px] text-gray-500 hover:text-gray-700 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Attach image</span>
                        <input
                            type="file"
                            wire:model="image"
                            accept="image/*"
                            class="hidden"
                        >
                    </label>
                </div>
            @endif

            @error('image')
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
                    <span wire:loading.remove wire:target="submit,image">Comment</span>
                    <span wire:loading wire:target="submit,image">Posting…</span>
                </button>
            </div>
        </div>
    </div>
</div>
