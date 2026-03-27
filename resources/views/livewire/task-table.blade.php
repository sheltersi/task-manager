   <div>
       {{-- Flash --}}
       @if (session('success'))
           <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
               {{ session('success') }}
           </div>
       @endif

       {{-- Task List --}}
       <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">

           @if ($tasks->isEmpty())
               <div class="text-center py-16 border border-dashed border-gray-200 rounded-2xl">
                   <p class="text-gray-500 text-sm mb-2">No tasks yet</p>
                   <a href="{{ route('tasks.create') }}" wire:navigate
                       class="text-sm text-black font-medium underline underline-offset-2">
                       Create your first task →
                   </a>
               </div>
           @else
               <table class="w-full text-sm">
                   <thead>
                       <tr class="border-b border-gray-100 bg-gray-50 text-xs uppercase tracking-widest text-gray-400">
                           <th class="px-5 py-3 text-left font-medium">Task</th>
                           <th class="px-5 py-3 text-left font-medium">Status</th>
                           <th class="px-5 py-3 text-left font-medium">Priority</th>
                           <th class="px-5 py-3 text-left font-medium">Due Date</th>
                           <th class="px-5 py-3 text-left font-medium">Created</th>
                           <th class="px-5 py-3 text-right font-medium">Actions</th>
                       </tr>
                   </thead>
                   <tbody class="divide-y divide-gray-50">
                       @foreach ($tasks as $task)
                           <tr class="group hover:bg-gray-50 transition-colors">

                               {{-- Title --}}
                               <td class="px-5 py-4">
                                   <a href="{{ route('tasks.show', $task) }}" wire:navigate
                                       class="font-semibold text-gray-900 hover:text-black">
                                       {{ $task->title }}
                                   </a>
                               </td>

                               {{-- Status --}}
                               <td class="px-5 py-4">
                                   <livewire:task-status-select :task="$task" :key="'status-' . $task->id" />
                               </td>

                               {{-- Priority --}}
                               <td class="px-5 py-4">
                                   <livewire:task-priority-select :task="$task" :key="'priority-' . $task->id" />
                               </td>

                               {{-- Due Date --}}
                               <td class="px-5 py-4">
                                   <div class="flex flex-col">
                                       <span class="text-gray-700">
                                           {{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}
                                       </span>
                                       @if ($task->due_date && $task->status !== 'completed' && $task->due_date->isPast())
                                           <span class="text-xs font-semibold text-red-500 mt-0.5">Overdue</span>
                                       @endif
                                   </div>
                               </td>

                               {{-- Created --}}
                               <td class="px-5 py-4 text-gray-400">
                                   {{ $task->created_at->diffForHumans() }}
                               </td>

                               {{-- Actions --}}
                               <td class="px-5 py-4">
                                   <div class="flex items-center justify-end gap-1">
                                       <a href="{{ route('tasks.show', $task) }}" wire:navigate
                                           class="px-3 py-1.5 rounded-lg text-green-500 bg-green-100 hover:bg-gray-100 hover:text-green-600 transition-colors">
                                           View
                                       </a>
                                       <a href="{{ route('tasks.edit', $task) }}" wire:navigate
                                           class="px-3 py-1.5 rounded-lg text-orange-400 bg-orange-100 hover:bg-gray-100 hover:text-orange-600 transition-colors">
                                           Edit
                                       </a>
                                       {{-- <button wire:click="delete({{ $task->id }})"
                                    class="px-3 py-1.5 rounded-lg text-red-400 bg-red-100 hover:bg-red-50 hover:text-red-500 transition-colors">
                                    Delete
                                </button> --}}
                                       <button wire:click="confirmDelete({{ $task->id }})"
                                           class="px-3 py-1.5 rounded-lg text-red-400 bg-red-100 hover:bg-red-50 hover:text-red-500 transition-colors">
                                           Delete
                                       </button>
                                   </div>
                               </td>

                           </tr>
                       @endforeach
                   </tbody>
               </table>
           @endif

       </div>

       {{-- Pagination --}}
       @if ($tasks->hasPages())
           <div class="flex justify-end pt-2">
               {{ $tasks->links('livewire::tailwind') }}
           </div>
       @endif

       {{-- Delete Confirmation Modal --}}
       <x-modal wire:model="deletingTaskId" maxWidth="md">
           <div class="p-6">
               <h2 class="text-base font-semibold text-gray-900 mb-1">Delete task?</h2>
               <p class="text-sm text-gray-500 mb-6">
                   This action cannot be undone. The task and all its subtasks will be permanently removed.
               </p>
               <div class="flex justify-end gap-3">
                   <button wire:click="cancelDelete"
                       class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                       Cancel
                   </button>
                   <button wire:click="delete" wire:loading.attr="disabled"
                       class="px-4 py-2 text-sm rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors disabled:opacity-50">
                       <span wire:loading.remove wire:target="delete">Delete task</span>
                       <span wire:loading wire:target="delete">Deleting...</span>
                   </button>
               </div>
           </div>
       </x-modal>

       {{-- Success Toast --}}
       <div x-data="{ show: false, message: '' }"
           x-on:task-deleted.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)"
           x-show="show" x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
           x-transition:leave-end="opacity-0 translate-y-2"
           class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl shadow-sm"
           style="display: none;" style="display: none;">
           <span class="text-emerald-400">✓</span>
           <span x-text="message"></span>
       </div>
   </div>
