<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
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

            {{-- Flash Message --}}
            @if (session('success'))
                <div
                    class="mb-4 rounded-lg bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 shadow rounded-xl p-4">
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Search
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by title or description..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Status
                        </label>
                        <select name="status"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All</option>
                            <option value="to_do" {{ request('status') === 'to_do' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In
                                Review</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                Completed</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Sort By
                        </label>
                        <select name="sort"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest
                            </option>
                            <option value="due_date" {{ request('sort') === 'due_date' ? 'selected' : '' }}>Due Date
                            </option>
                            <option value="priority" {{ request('sort') === 'priority' ? 'selected' : '' }}>Priority
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-4 flex items-center gap-3">
                        <button type="submit"
                            class="px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-sm font-semibold">
                            Apply
                        </button>

                        <a href="{{ route('dashboard') }}"
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-semibold">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tasks Table --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                    Title
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                    Priority
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                    Due Date
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($tasks as $task)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $task->title }}
                                        </div>

                                        @if ($task->description)
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
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
                                                class="text-xs font-semibold rounded-full border-gray-300 dark:bg-gray-900
                                                    {{ $task->status === 'completed' ? 'text-green-600' : 'text-gray-600' }}">
                                                <option value="to_do"
                                                    {{ $task->status == 'to_do' ? 'selected' : '' }}>To Do</option>
                                                <option value="in_progress"
                                                    {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress
                                                </option>
                                                <option value="in_review"
                                                    {{ $task->status == 'in_review' ? 'selected' : '' }}>In Review
                                                </option>
                                                <option value="completed"
                                                    {{ $task->status == 'completed' ? 'selected' : '' }}>Completed
                                                </option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- Priority Column --}}
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('tasks.update-priority', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="priority" onchange="this.form.submit()"
                                                class="text-xs border-none bg-transparent font-semibold dark:text-gray-200">
                                                <option value="1" {{ $task->priority == 1 ? 'selected' : '' }}>1 -
                                                    Lowest</option>
                                                <option value="2" {{ $task->priority == 2 ? 'selected' : '' }}>2 -
                                                    Low</option>
                                                <option value="3" {{ $task->priority == 3 ? 'selected' : '' }}>3 -
                                                    Medium</option>
                                                <option value="4" {{ $task->priority == 4 ? 'selected' : '' }}>4 -
                                                    High</option>
                                                <option value="5" {{ $task->priority == 5 ? 'selected' : '' }}>5 -
                                                    Highest</option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- Due Date --}}
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}
                                        </span>

                                        {{-- Overdue indicator --}}
                                        @if ($task->due_date && $task->status !== 'completed' && $task->due_date->isPast())
                                            <span class="ml-2 text-xs font-semibold text-red-600 dark:text-red-400">
                                                Overdue
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end items-center gap-2">

                                             {{-- Edit --}}
                                            <a href="{{ route('tasks.show', $task) }}"
                                                class="px-3 py-2 rounded-lg text-xs font-semibold bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-900/50 text-green-800 dark:text-green-200">
                                                View
                                            </a>


                                            {{-- Edit --}}
                                            <a href="{{ route('tasks.edit', $task) }}"
                                                class="px-3 py-2 rounded-lg text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200">
                                                Edit
                                            </a>

                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-2 rounded-lg text-xs font-semibold bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-800 dark:text-red-200">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
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
