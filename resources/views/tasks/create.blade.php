<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                    {{ __('Create Task') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Add a new task and set its workflow stage, priority, and due date.
                </p>
            </div>

            <a href="{{ route('tasks.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition shadow-sm">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="py-10 ">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Card --}}
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                {{-- Top glow --}}
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                <form method="POST" action="{{ route('tasks.store') }}" class="p-6 sm:p-8">
                    @csrf

                    {{-- Header --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Task Details</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Keep the title short. You can always add more detail in the description.
                        </p>
                    </div>

                    <div class="space-y-6">

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Task Title <span class="text-red-500">*</span>
                            </label>

                            <div class="mt-2">
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title') }}"
                                       placeholder="e.g. Finish the Q1 Report"
                                       class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white
                                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                       required autofocus>
                            </div>

                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Description <span class="text-gray-400">(optional)</span>
                            </label>

                            <div class="mt-2">
                                <textarea name="description"
                                          id="description"
                                          rows="4"
                                          placeholder="What needs to be done?"
                                          class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white
                                                 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">{{ old('description') }}</textarea>
                            </div>

                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status / Priority / Due Date --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Initial Status
                                </label>

                                <div class="mt-2">
                                    <select name="status"
                                            id="status"
                                            class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white
                                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                        <option value="to_do" {{ old('status', 'to_do') == 'to_do' ? 'selected' : '' }}>To Do</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="in_review" {{ old('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>

                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Priority --}}
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Priority
                                </label>

                                <div class="mt-2">
                                    <select name="priority"
                                            id="priority"
                                            class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white
                                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                        <option value="1" {{ old('priority') == 1 ? 'selected' : '' }}>1 — Lowest</option>
                                        <option value="2" {{ old('priority') == 2 ? 'selected' : '' }}>2 — Low</option>
                                        <option value="3" {{ old('priority', 3) == 3 ? 'selected' : '' }}>3 — Medium</option>
                                        <option value="4" {{ old('priority') == 4 ? 'selected' : '' }}>4 — High</option>
                                        <option value="5" {{ old('priority') == 5 ? 'selected' : '' }}>5 — Highest</option>
                                    </select>
                                </div>

                                @error('priority')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Due Date --}}
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Due Date
                                </label>

                                <div class="mt-2">
                                    <input type="date"
                                           name="due_date"
                                           id="due_date"
                                           value="{{ old('due_date') }}"
                                           class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white
                                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                </div>

                                @error('due_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Tip: Use “To Do” for new tasks and “In Review” when waiting for approval.
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('tasks.index') }}"
                               class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700
                                      text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/60 transition">
                                Back to List
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700
                                           text-white font-semibold shadow-sm transition transform hover:-translate-y-0.5 active:translate-y-0">
                                Create Task
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
