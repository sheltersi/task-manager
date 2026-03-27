<div class="mx-4 md:mx-24 my-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dashboard') }}" wire:navigate
            class="w-[30px] h-[30px] flex items-center justify-center border border-black/[0.07] rounded-lg bg-white text-gray-400 hover:text-gray-700 text-sm transition-colors">
            ←
        </a>
        <h2 class="text-[16px] font-medium text-gray-900">
            {{ $task ? 'Edit task' : 'New task' }}
        </h2>
    </div>

    {{-- Card --}}
    <form wire:submit="save" class="bg-white border border-black/[0.07] rounded-xl p-6">
        <div class="grid grid-cols-2 gap-4">

            {{-- Title --}}
            <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Title</label>
                <input wire:model="title" type="text" placeholder="What needs to be done?"
                    class="h-10 border border-black/[0.08] rounded-lg px-3 text-[14px] text-gray-900 outline-none focus:border-black/25 placeholder-gray-300" />
                @error('title')
                    <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Description</label>
                <textarea wire:model="description" placeholder="Add more context…" rows="3"
                    class="border border-black/[0.08] rounded-lg px-3 py-2.5 text-[13px] text-gray-900 outline-none focus:border-black/25 placeholder-gray-300 resize-none leading-relaxed"></textarea>
                @error('description')
                    <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image --}}
            <div class="col-span-2 flex flex-col gap-1.5" x-data="{ localPreview: null }">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Attachment</label>

                {{-- Preview: show local preview first, fall back to server preview --}}
                <template x-if="localPreview">
                    <div class="relative inline-block">
                        <img :src="localPreview" alt="Image preview"
                            class="h-32 w-32 object-cover rounded-lg border border-black/[0.08]">
                        <button type="button" @click="localPreview = null; $wire.removeImage()"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-[12px] hover:bg-red-600">
                            ×
                        </button>
                    </div>
                </template>

                {{-- @if ($imagePreview && !isset($localPreview))
                    <div class="relative inline-block">
                        <img src="{{ $imagePreview }}" alt="Image preview"
                            class="h-32 w-32 object-cover rounded-lg border border-black/[0.08]">
                        <button type="button" wire:click="removeImage"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-[12px] hover:bg-red-600">
                            ×
                        </button>
                    </div>
                @endif --}}
                @if ($imagePreview)
                    <div class="relative inline-block" x-show="!localPreview">
                        <img src="{{ $imagePreview }}" alt="Image preview"
                            class="h-32 w-32 object-cover rounded-lg border border-black/[0.08]">
                        <button type="button" wire:click="removeImage"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-[12px] hover:bg-red-600">
                            ×
                        </button>
                    </div>
                @endif

                {{-- Loading overlay --}}
<div wire:loading wire:target="image"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class=" rounded-xl px-6 py-4 mt-[350px] flex items-center gap-3 shadow-lg">
        <svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        <span class="text-[13px] font-medium text-white">Uploading image…</span>
    </div>
</div>


                {{-- Upload button --}}
                {{-- <template x-if="!localPreview">
                    <label
                        class="inline-flex items-center gap-2 px-4 py-2.5 border border-black/[0.08] rounded-lg text-[13px] text-gray-500 hover:text-gray-700 hover:border-black/20 cursor-pointer w-fit transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Attach image</span>
                        <input type="file" wire:model="image" accept="image/*" class="hidden"
                            @change="
                    const file = $event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => localPreview = e.target.result;
                        reader.readAsDataURL(file);
                    }
                ">
                    </label>
                </template> --}}

                <template x-if="!localPreview">
    <label class="inline-flex items-center gap-2 px-4 py-2.5 border border-black/[0.08] rounded-lg text-[13px] text-gray-500 hover:text-gray-700 hover:border-black/20 cursor-pointer w-fit transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span>Attach image</span>
        <input
            type="file"
            wire:model="image"
            accept="image/*"
            class="hidden"
            @change="
                const file = $event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => localPreview = e.target.result;
                    reader.readAsDataURL(file);
                }
            "
        >
    </label>
</template>

                @error('image')
                    <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image --}}
            {{-- <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Attachment</label> --}}

            {{-- Image preview --}}
            {{-- @if ($imagePreview)
                    <div class="relative inline-block">
                        <img src="{{ $imagePreview }}" alt="Image preview" class="h-32 w-32 object-cover rounded-lg border border-black/[0.08]">
                        <button
                            type="button"
                            wire:click="removeImage"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-[12px] hover:bg-red-600">
                            ×
                        </button>
                    </div>
                @endif --}}

            {{-- Image upload button --}}
            {{-- @if (!$imagePreview)
                    <label class="inline-flex items-center gap-2 px-4 py-2.5 border border-black/[0.08] rounded-lg text-[13px] text-gray-500 hover:text-gray-700 hover:border-black/20 cursor-pointer w-fit transition-colors">
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
                @endif

                @error('image')
                    <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror --}}
            {{-- </div> --}}

            {{-- Status --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Status</label>
                <select wire:model="status"
                    class="h-[36px] border border-black/[0.08] rounded-lg px-3 text-[13px] text-gray-900 outline-none focus:border-black/25 bg-white">
                    <option value="to_do">To do</option>
                    <option value="in_progress">In progress</option>
                    <option value="in_review">In review</option>
                    <option value="completed">Completed</option>
                </select>
                @error('status')
                    <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Due date --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Due date</label>
                <input wire:model="due_date" type="date"
                    class="h-[36px] border border-black/[0.08] rounded-lg px-3 text-[13px] text-gray-900 outline-none focus:border-black/25" />
                @error('due_date')
                    <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Priority --}}
            <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Priority</label>
                <div class="flex gap-1.5">
                    @foreach (range(1, 5) as $p)
                        <button type="button" wire:click="$set('priority', {{ $p }})"
                            @class([
                                'w-8 h-8 rounded-lg border text-[12px] font-mono font-medium transition-colors',
                                'bg-gray-900 text-white border-gray-900' => $priority === $p,
                                'bg-gray-50 text-gray-400 border-black/[0.08] hover:border-black/20' =>
                                    $priority !== $p,
                            ])>{{ $p }}</button>
                    @endforeach
                </div>
                @error('priority')
                    <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-black/[0.06]">
            <a href="{{ route('dashboard') }}" wire:navigate wire:click="cancel"
                class="h-[34px] px-4 border border-black/[0.08] rounded-lg text-[13px] font-medium text-gray-400 hover:text-gray-700 flex items-center transition-colors">
                Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="h-[34px] px-5 bg-gray-900 text-white rounded-lg text-[13px] font-medium hover:bg-gray-800 disabled:opacity-50 transition-colors">
                <span wire:loading.remove wire:target="save,image">Save task</span>
                <span wire:loading wire:target="save,image">Saving…</span>
            </button>
        </div>
    </form>
</div>
