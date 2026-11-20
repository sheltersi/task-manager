<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tasks') }}
            </h2>

            <a href="{{ route('tasks.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow">
                + New Task
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="mb-6 bg-white border border-gray-200 rounded-xl p-4">
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Search
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by title or description..."
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>
                        <select name="status"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All</option>
                            <option value="to_do" {{ request('status') === 'to_do' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sort By
                        </label>
                        <select name="sort"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="due_date" {{ request('sort') === 'due_date' ? 'selected' : '' }}>Due Date</option>
                            <option value="priority" {{ request('sort') === 'priority' ? 'selected' : '' }}>Priority</option>
                        </select>
                    </div>

                    <div class="md:col-span-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg text-sm font-semibold">
                                Apply
                            </button>

                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm font-semibold">
                                Reset
                            </a>
                        </div>

                        <a href="{{ route('timesheets.index') }}"
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-700 whitespace-nowrap shrink-0">
                            Open Weekly Timesheets →
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tasks Table --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">
                                    Title
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">
                                    Logged
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">
                                    Due Date
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($tasks as $task)
                                @php $loggedHrs = (float) ($task->worklogs_sum_hours ?? 0); @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs text-gray-400 tabular-nums shrink-0">TM-{{ $task->id }}</span>
                                            <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                                {{ $task->title }}
                                            </a>
                                        </div>

                                        @if ($task->description)
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ Str::limit($task->description, 70) }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Status Column --}}
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('tasks.update-status', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                class="text-xs font-semibold rounded-lg border-gray-300 {{ $task->status === 'completed' ? 'text-green-600' : 'text-gray-600' }}">
                                                <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }}>To Do</option>
                                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="in_review" {{ $task->status == 'in_review' ? 'selected' : '' }}>In Review</option>
                                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- Priority Column --}}
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('tasks.update-priority', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="priority" onchange="this.form.submit()"
                                                class="text-xs border-none bg-transparent font-semibold">
                                                <option value="1" {{ $task->priority == 1 ? 'selected' : '' }}>1 - Lowest</option>
                                                <option value="2" {{ $task->priority == 2 ? 'selected' : '' }}>2 - Low</option>
                                                <option value="3" {{ $task->priority == 3 ? 'selected' : '' }}>3 - Medium</option>
                                                <option value="4" {{ $task->priority == 4 ? 'selected' : '' }}>4 - High</option>
                                                <option value="5" {{ $task->priority == 5 ? 'selected' : '' }}>5 - Highest</option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- Logged Hours --}}
                                    <td class="px-6 py-4">
                                        <a href="{{ route('timesheets.index', ['taskId' => $task->id]) }}"
                                           class="font-mono tabular-nums text-xs font-medium {{ $loggedHrs > 0 ? 'text-indigo-700 hover:underline' : 'text-gray-400 hover:text-gray-600' }}">
                                            {{ $loggedHrs > 0 ? number_format($loggedHrs, 1).'h' : 'Log time' }}
                                        </a>
                                    </td>

                                    {{-- Due Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-700 font-mono tabular-nums">
                                            {{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}
                                        </span>

                                        @if ($task->due_date && $task->status !== 'completed' && $task->due_date->isPast())
                                            <span class="ml-2 text-xs font-semibold text-red-600">
                                                Overdue
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('timesheets.index', ['taskId' => $task->id]) }}"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 whitespace-nowrap shrink-0">
                                                Log Time
                                            </a>

                                            <a href="{{ route('tasks.show', $task) }}"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-100 hover:bg-green-200 text-green-800 whitespace-nowrap shrink-0">
                                                View
                                            </a>

                                            <a href="{{ route('tasks.edit', $task) }}"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-yellow-100 hover:bg-yellow-200 text-yellow-800 whitespace-nowrap shrink-0">
                                                Edit
                                            </a>

                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                                class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="delete-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 hover:bg-red-200 text-red-800 whitespace-nowrap shrink-0">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        No tasks found. Create your first task 🚀
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4">
                    {{ $tasks->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
