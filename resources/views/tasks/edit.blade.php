<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    {{ __('Edit Task') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Update details for:
                    <span class="font-semibold text-indigo-600">{{ $task->title }}</span>
                </p>
            </div>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition shadow-sm">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                <form method="POST" action="{{ route('tasks.update', $task) }}" class="p-6 sm:p-8">
                    @csrf
                    @method('PUT')

                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900">Task Details</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Keep it clear and actionable. You can also change the workflow stage and priority.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Task Title <span class="text-red-500">*</span>
                            </label>

                            <div class="mt-2 relative">
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title', $task->title) }}"
                                       class="block w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                       placeholder="e.g. Finish internship assessment"
                                       required>
                            </div>

                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description <span class="text-gray-400">(optional)</span>
                            </label>

                            <div class="mt-2">
                                <textarea name="description"
                                          id="description"
                                          rows="4"
                                          class="block w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                          placeholder="Add extra context or steps...">{{ old('description', $task->description) }}</textarea>
                            </div>

                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>

                                <div class="mt-2">
                                    <select name="status"
                                            id="status"
                                            class="block w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                        @foreach(['to_do' => 'To Do', 'in_progress' => 'In Progress', 'in_review' => 'In Review', 'completed' => 'Completed'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', $task->status) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">
                                    Priority
                                </label>

                                <div class="mt-2">
                                    <select name="priority"
                                            id="priority"
                                            class="block w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                        <option value="1" {{ old('priority', $task->priority) == 1 ? 'selected' : '' }}>1 — Lowest</option>
                                        <option value="2" {{ old('priority', $task->priority) == 2 ? 'selected' : '' }}>2 — Low</option>
                                        <option value="3" {{ old('priority', $task->priority) == 3 ? 'selected' : '' }}>3 — Medium</option>
                                        <option value="4" {{ old('priority', $task->priority) == 4 ? 'selected' : '' }}>4 — High</option>
                                        <option value="5" {{ old('priority', $task->priority) == 5 ? 'selected' : '' }}>5 — Highest</option>
                                    </select>
                                </div>

                                @error('priority')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">
                                    Due Date
                                </label>

                                <div class="mt-2">
                                    <input type="date"
                                           name="due_date"
                                           id="due_date"
                                           value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                                           class="block w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                </div>

                                @error('due_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs text-gray-500">
                            Tip: Keep tasks short and use “In Review” when you’re waiting for approval.
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <button type="submit"
                                    class="inline-flex items-center px-8 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                                Update Task
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
