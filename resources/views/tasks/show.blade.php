<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                    {{ __('Task Details') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    View task information, status, and actions.
                </p>
            </div>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition shadow-sm">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @php
                $statusLabel = match($task->status) {
                    'to_do' => 'To Do',
                    'in_progress' => 'In Progress',
                    'in_review' => 'In Review',
                    'completed' => 'Completed',
                    default => ucfirst(str_replace('_',' ', $task->status)),
                };

                $statusClass = match($task->status) {
                    'to_do' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-600',
                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200 border-blue-200 dark:border-blue-800',
                    'in_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200 border-yellow-200 dark:border-yellow-800',
                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200 border-green-200 dark:border-green-800',
                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-600',
                };

                $priorityLabel = match($task->priority) {
                    1 => 'Lowest',
                    2 => 'Low',
                    3 => 'Medium',
                    4 => 'High',
                    5 => 'Highest',
                    default => 'N/A',
                };

                $isOverdue = $task->due_date && $task->status !== 'completed' && $task->due_date->isPast();
            @endphp

            {{-- Card --}}
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                {{-- Top glow --}}
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                {{-- Header strip --}}
                <div class="px-6 sm:px-8 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>

                        @if($isOverdue)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border
                                         bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200 border-red-200 dark:border-red-800">
                                Overdue
                            </span>
                        @endif
                    </div>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Created <span class="font-medium text-gray-700 dark:text-gray-200">{{ $task->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    {{-- Title --}}
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $task->title }}
                    </h1>

                    {{-- Meta cards --}}
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/40 p-4">
                            <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 font-semibold">
                                Priority
                            </p>
                            <p class="mt-2 text-gray-900 dark:text-gray-100 font-semibold">
                                {{ $priorityLabel }}
                                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">(Level {{ $task->priority }})</span>
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/40 p-4">
                            <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 font-semibold">
                                Due Date
                            </p>
                            <p class="mt-2 text-gray-900 dark:text-gray-100 font-semibold">
                                {{ $task->due_date ? $task->due_date->format('d M Y') : 'No deadline' }}
                            </p>
                            @if($task->due_date)
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $task->due_date->diffForHumans() }}
                                </p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/40 p-4">
                            <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 font-semibold">
                                Last Updated
                            </p>
                            <p class="mt-2 text-gray-900 dark:text-gray-100 font-semibold">
                                {{ $task->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mt-8">
                        <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 font-semibold">
                            Description
                        </p>

                        <div class="mt-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-5">
                            @if($task->description)
                                <div class="text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-line">
                                    {{ $task->description }}
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 italic">
                                    No description provided.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    

                        <div class="flex items-center gap-3">
                            <a href="{{ route('tasks.edit', $task) }}"
                               class="inline-flex items-center px-4 py-2 rounded-xl bg-yellow-100 dark:bg-yellow-900/30
                                      text-yellow-800 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition font-semibold">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('tasks.destroy', $task) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 rounded-xl bg-red-100 dark:bg-red-900/30
                                               text-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-900/50 transition font-semibold">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
