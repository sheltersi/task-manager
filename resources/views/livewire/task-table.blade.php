   <div>
   {{-- Flash --}}
    @if (session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

   {{-- Task List --}}
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
                                <button wire:click="delete({{ $task->id }})"
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
</div>
