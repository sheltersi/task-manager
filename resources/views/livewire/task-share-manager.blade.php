<div>
    {{-- Share button — only visible to owner --}}
    @if($task->user_id === auth()->id())
        <button
            wire:click="openModal"
            class="h-[32px] px-4 border border-black/[0.08] rounded-lg text-[13px] font-medium text-gray-500 hover:text-gray-800 flex items-center gap-1.5 transition-colors">
            Share
            @if($this->collaborators->count() > 0)
                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-gray-900 text-white text-[10px]">
                    {{ $this->collaborators->count() }}
                </span>
            @endif
        </button>
    @endif

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50"
             wire:click.self="closeModal">
            <div class="bg-white rounded-xl border border-black/[0.07] w-full max-w-md p-6 mx-4">

                {{-- Modal header --}}
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-[15px] font-medium text-gray-900">Share task</h3>
                    <button wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-700 text-[18px] leading-none transition-colors">
                        ✕
                    </button>
                </div>

                {{-- Invite form --}}
                <div class="flex gap-2 mb-5">
                    <div class="flex-1">
                        <input
                            wire:model="email"
                            type="email"
                            placeholder="Email address"
                            class="w-full h-[36px] border border-black/[0.08] rounded-lg px-3 text-[13px] text-gray-900 outline-none focus:border-black/25 placeholder-gray-300"
                        />
                        @error('email')
                            <p class="mt-1 text-[12px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <select
                        wire:model="role"
                        class="h-[36px] border border-black/[0.08] rounded-lg px-3 text-[13px] text-gray-600 bg-white outline-none min-w-[110px]">
                        <option value="commenter">Commenter</option>
                        <option value="viewer">Viewer</option>
                    </select>
                    <button
                        wire:click="invite"
                        wire:loading.attr="disabled"
                        class="h-[36px] px-4 bg-gray-900 text-white rounded-lg text-[13px] font-medium hover:bg-gray-800 disabled:opacity-50 transition-colors whitespace-nowrap">
                        <span wire:loading.remove wire:target="invite">Invite</span>
                        <span wire:loading wire:target="invite">Inviting…</span>
                    </button>
                </div>

                {{-- Current collaborators --}}
                @if($this->collaborators->count() > 0)
                    <div class="border-t border-black/[0.06] pt-4">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400 mb-3">
                            People with access
                        </p>
                        <div class="flex flex-col gap-3">
                            @foreach($this->collaborators as $share)
                                <div class="flex items-center justify-between gap-3">

                                    {{-- User info --}}
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 border border-black/[0.07] flex items-center justify-center text-[11px] font-medium text-gray-500">
                                            {{ strtoupper(substr($share->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-medium text-gray-900">{{ $share->user->name }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $share->user->email }}</p>
                                        </div>
                                    </div>

                                    {{-- Role + remove --}}
                                    <div class="flex items-center gap-2">
                                        <select
                                            wire:change="updateRole({{ $share->user->id }}, $event.target.value)"
                                            class="h-[28px] border border-black/[0.07] rounded-md px-2 text-[11px] text-gray-600 bg-white outline-none">
                                            <option value="commenter" @selected($share->role === 'commenter')>Commenter</option>
                                            <option value="viewer" @selected($share->role === 'viewer')>Viewer</option>
                                        </select>
                                        <button
                                            wire:click="removeCollaborator({{ $share->user->id }})"
                                            wire:confirm="Remove {{ $share->user->name }}?"
                                            class="text-[11px] text-gray-400 hover:text-red-500 transition-colors">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
