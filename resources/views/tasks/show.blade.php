<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Task Details') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    TM-{{ $task->id }} · View task information, time logs, and actions.
                </p>
            </div>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition shadow-sm">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $statusLabel = match($task->status) {
                    'to_do' => 'To Do',
                    'in_progress' => 'In Progress',
                    'in_review' => 'In Review',
                    'completed' => 'Completed',
                    default => ucfirst(str_replace('_',' ', $task->status)),
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

            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                <div class="px-6 sm:px-8 py-4 border-b border-gray-200 bg-gray-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <span class="font-mono text-gray-500">TM-{{ $task->id }}</span>
                        <span aria-hidden="true">·</span>
                        <span class="{{ $task->status === 'completed' ? 'text-green-700 font-semibold' : 'text-gray-800 font-semibold' }}">
                            {{ $statusLabel }}
                        </span>
                        @if($isOverdue)
                            <span aria-hidden="true">·</span>
                            <span class="font-semibold text-red-600">Overdue</span>
                        @endif
                        <span aria-hidden="true">·</span>
                        <span class="font-mono tabular-nums text-indigo-700 font-semibold">{{ number_format($totalTaskHours, 1) }}h logged</span>
                    </div>

                    <div class="text-sm text-gray-500">
                        Created <span class="font-medium text-gray-700">{{ $task->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        {{ $task->title }}
                    </h1>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-b border-gray-100 py-5">
                        <div>
                            <p class="text-xs font-medium text-gray-500">Priority</p>
                            <p class="mt-1 text-gray-900 font-semibold">
                                {{ $priorityLabel }}
                                <span class="text-sm text-gray-500 font-normal font-mono tabular-nums">(Level {{ $task->priority }})</span>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500">Due Date</p>
                            <p class="mt-1 text-gray-900 font-semibold font-mono tabular-nums">
                                {{ $task->due_date ? $task->due_date->format('d M Y') : 'No deadline' }}
                            </p>
                            @if($task->due_date)
                                <p class="mt-0.5 text-xs text-gray-500">{{ $task->due_date->diffForHumans() }}</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500">Total Logged Time</p>
                            <p class="mt-1 text-gray-900 font-semibold font-mono tabular-nums">
                                {{ number_format($totalTaskHours, 1) }}h
                            </p>
                            <p class="mt-0.5 text-xs text-gray-500 font-mono tabular-nums">
                                {{ $task->worklogs->count() }} {{ Str::plural('entry', $task->worklogs->count()) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-medium text-gray-500">Description</p>
                        <div class="mt-2">
                            @if($task->description)
                                <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $task->description }}</div>
                            @else
                                <p class="text-gray-500 italic">No description provided.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('tasks.edit', $task) }}"
                               class="inline-flex items-center px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition font-semibold text-sm">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="delete-btn inline-flex items-center px-4 py-2 rounded-lg bg-red-100 text-red-800 hover:bg-red-200 transition font-semibold text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>

                        <a href="{{ route('timesheets.index', ['taskId' => $task->id]) }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
                            Open in Timesheets →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Worklogs Section --}}
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 sm:px-8 py-4 border-b border-gray-200 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Worklogs</h3>
                        <p class="text-xs text-gray-500">Log time directly on this task or review recorded entries.</p>
                    </div>
                    <span class="font-mono tabular-nums text-sm font-semibold text-gray-900">
                        Total: {{ number_format($totalTaskHours, 1) }}h
                    </span>
                </div>

                <form method="POST" action="{{ route('timesheets.worklogs.store') }}" class="px-6 sm:px-8 py-5 bg-gray-50/70 border-b border-gray-200">
                    @csrf
                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                    <input type="hidden" name="return_to" value="{{ route('tasks.show', $task) }}">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-3">
                            <label for="work_date" class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                            <input id="work_date" type="date" name="work_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full rounded-lg border-gray-300 text-sm font-mono tabular-nums focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="hours" class="block text-xs font-medium text-gray-600 mb-1">Hours</label>
                            <input id="hours" type="number" name="hours" step="0.25" min="0.25" max="24" value="1.0" required
                                   class="w-full rounded-lg border-gray-300 text-sm font-mono tabular-nums focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="sm:col-span-4">
                            <label for="wl_description" class="block text-xs font-medium text-gray-600 mb-1">Work description</label>
                            <input id="wl_description" type="text" name="description" placeholder="What did you work on?"
                                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="sm:col-span-1 flex items-center h-10">
                            <label class="inline-flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                                <input type="checkbox" name="billable" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span>Billable</span>
                            </label>
                        </div>
                        <div class="sm:col-span-2 flex justify-end">
                            <button type="submit"
                                    class="w-full px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold whitespace-nowrap">
                                Log Time
                            </button>
                        </div>
                    </div>
                </form>

                @forelse ($task->worklogs as $entry)
                    <div class="px-6 sm:px-8 py-4 border-b last:border-b-0 border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="font-mono tabular-nums text-gray-800 font-medium">{{ $entry->work_date->format('Y-m-d') }}</span>
                                <span aria-hidden="true">·</span>
                                <span class="font-mono tabular-nums">{{ $entry->started_at }}</span>
                                <span aria-hidden="true">·</span>
                                <span class="{{ $entry->billable ? 'text-emerald-700 font-medium' : 'text-gray-500' }}">
                                    {{ $entry->billable ? 'Billable' : 'Non-billable' }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $entry->description ?: 'No description provided' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4 shrink-0">
                            <span class="font-mono tabular-nums text-sm font-semibold text-gray-900">
                                {{ number_format($entry->hours, 1) }}h
                            </span>

                            <form method="POST" action="{{ route('timesheets.worklogs.destroy', $entry) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 sm:px-8 py-8 text-center text-sm text-gray-500">
                        No time logged on this task yet. Use the form above to record your first worklog.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
