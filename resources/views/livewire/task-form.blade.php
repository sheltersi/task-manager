<div class="mx-4 md:mx-24 my-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dashboard') }}"
           wire:navigate
           class="w-[30px] h-[30px] flex items-center justify-center border border-black/[0.07] rounded-lg bg-white text-gray-400 hover:text-gray-700 text-sm transition-colors">
            ←
        </a>
        <h2 class="text-[16px] font-medium text-gray-900">
            {{ $task ? 'Edit task' : 'New task' }}
        </h2>
    </div>

    {{-- Card --}}
    <div class="bg-white border border-black/[0.07] rounded-xl p-6">
        <div class="grid grid-cols-2 gap-4">

            {{-- Title --}}
            <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Title</label>
                <input
                    wire:model="title"
                    type="text"
                    placeholder="What needs to be done?"
                    class="h-10 border border-black/[0.08] rounded-lg px-3 text-[14px] text-gray-900 outline-none focus:border-black/25 placeholder-gray-300"
                />
                @error('title') <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Description</label>
                <textarea
                    wire:model="description"
                    placeholder="Add more context…"
                    rows="3"
                    class="border border-black/[0.08] rounded-lg px-3 py-2.5 text-[13px] text-gray-900 outline-none focus:border-black/25 placeholder-gray-300 resize-none leading-relaxed"
                ></textarea>
                @error('description') <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Status</label>
                <select
                    wire:model="status"
                    class="h-[36px] border border-black/[0.08] rounded-lg px-3 text-[13px] text-gray-900 outline-none focus:border-black/25 bg-white"
                >
                    <option value="to_do">To do</option>
                    <option value="in_progress">In progress</option>
                    <option value="in_review">In review</option>
                    <option value="completed">Completed</option>
                </select>
                @error('status') <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p> @enderror
            </div>

            {{-- Due date --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Due date</label>
                <input
                    wire:model="due_date"
                    type="date"
                    class="h-[36px] border border-black/[0.08] rounded-lg px-3 text-[13px] text-gray-900 outline-none focus:border-black/25"
                />
                @error('due_date') <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p> @enderror
            </div>

            {{-- Priority --}}
            <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Priority</label>
                <div class="flex gap-1.5">
                    @foreach(range(1, 5) as $p)
                    <button
                        type="button"
                        wire:click="$set('priority', {{ $p }})"
                        @class([
                            'w-8 h-8 rounded-lg border text-[12px] font-mono font-medium transition-colors',
                            'bg-gray-900 text-white border-gray-900' => $priority === $p,
                            'bg-gray-50 text-gray-400 border-black/[0.08] hover:border-black/20' => $priority !== $p,
                        ])
                    >{{ $p }}</button>
                    @endforeach
                </div>
                @error('priority') <p class="text-[12px] text-red-500 mt-0.5">{{ $message }}</p> @enderror
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-black/[0.06]">
            <a href="{{ route('dashboard') }}"
               wire:navigate
               class="h-[34px] px-4 border border-black/[0.08] rounded-lg text-[13px] font-medium text-gray-400 hover:text-gray-700 flex items-center transition-colors">
                Cancel
            </a>
            <button
                wire:click="save"
                wire:loading.attr="disabled"
                class="h-[34px] px-5 bg-gray-900 text-white rounded-lg text-[13px] font-medium hover:bg-gray-800 disabled:opacity-50 transition-colors"
            >
                <span wire:loading.remove>Save task</span>
                <span wire:loading>Saving…</span>
            </button>
        </div>
    </div>
</div>
