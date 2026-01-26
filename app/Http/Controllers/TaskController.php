<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

 /**
     * Display a listing of the user's tasks.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $search = $request->query('search');
        $status = $request->query('status'); // pending | completed | null
        $sort   = $request->query('sort', 'latest'); // latest | due_date | priority

        $query = $user->tasks()->query();

        // Search by title/description
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // Sorting
        if ($sort === 'due_date') {
            $query->orderByRaw('due_date IS NULL') // push nulls to bottom
                  ->orderBy('due_date', 'asc');
        } elseif ($sort === 'priority') {
            $query->orderBy('priority', 'desc');
        } else {
            $query->latest();
        }

        $tasks = $query->paginate(10)->withQueryString();

        return view('tasks.index', compact('tasks', 'search', 'status', 'sort'));
    }

/**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return view('tasks.create');
    }

    public function store(TaskRequest $request)
{
    $request->user()->tasks()->create($request->validated());

    return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
}


    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        // Prevent editing other users' tasks
        $this->authorize('update', $task);

        return view('tasks.edit', compact('task'));
    }


public function update(TaskRequest $request, Task $task)
{
    $this->authorize('update', $task);

    $task->update($request->validated());

    return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
}
/**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

public function advance(Task $task)
{
    $this->authorize('update', $task);

    $flow = ['to_do', 'in_progress', 'in_review', 'completed'];

    $currentIndex = array_search($task->status, $flow, true);

    // If status is invalid, reset to first stage
    if ($currentIndex === false) {
        $task->update(['status' => 'to_do']);
        return back()->with('success', 'Task status reset to To Do.');
    }

    // If already completed, keep it completed (or reset if you want)
    if ($task->status === 'completed') {
        return back()->with('success', 'Task is already completed.');
    }

    $nextStatus = $flow[$currentIndex + 1];

    $task->update(['status' => $nextStatus]);

    return back()->with('success', 'Task moved to the next stage.');
}


   public function regress(Task $task)
{
    $this->authorize('update', $task);

    $flow = ['to_do', 'in_progress', 'in_review', 'completed'];

    $currentIndex = array_search($task->status, $flow, true);

    // If status is invalid, reset to first stage
    if ($currentIndex === false) {
        $task->update(['status' => 'to_do']);
        return back()->with('success', 'Task status reset to To Do.');
    }

    // If already at the first stage, do nothing
    if ($currentIndex === 0) {
        return back()->with('success', 'Task is already at To Do.');
    }

    $previousStatus = $flow[$currentIndex - 1];

    $task->update(['status' => $previousStatus]);

    return back()->with('success', 'Task moved to the previous stage.');
}


}
