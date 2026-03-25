<div>
    <select wire:model.live="status"
        class="text-xs font-semibold rounded-full border-gray-300 dark:bg-gray-900">
        {{ $task->status === 'completed' ? 'text-green-600' : 'text-gray-600' }}">
        <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }}>To do</option>
        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
        <option value="in_review" {{ $task->status == 'in_review' ? 'selected' : '' }}>In review</option>
        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
    </select>
</div>
