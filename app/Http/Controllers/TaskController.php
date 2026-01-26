<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{

    /**
     * Display a listing of the user's tasks.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $search = $request->input('search');
        $status = $request->input('status');
        $sort   = $request->input('sort', 'latest');

        $query = $user->tasks();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        //Filter
        $allowedStatuses = ['to_do', 'in_progress', 'in_review', 'completed'];

        $query->when(in_array($status, $allowedStatuses), function ($q) use ($status) {
            return $q->where('status', $status);
        });

        // Sorting
        match ($sort) {
            'due_date' => $query->orderByRaw('due_date IS NULL')->orderBy('due_date', 'asc'),
            'priority' => $query->orderBy('priority', 'desc'),
            default    => $query->latest(),
        };

        $tasks = $query->paginate(10)->withQueryString();

        return view('dashboard', compact('tasks', 'search', 'status', 'sort'));
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

        return redirect()->route('dashboard')->with('success', 'Task created successfully.');
    }


    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        // Prevent editing other users' tasks
        Gate::authorize('update', $task);

        return view('tasks.edit', compact('task'));
    }


    public function update(TaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('dashboard')->with('success', 'Task updated successfully.');
    }

    /**
     * Display the specified task details.
     */
    public function show(Task $task)
    {

        Gate::authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        // Validate that the status is one of your allowed types
        $validated = $request->validate([
            'status' => 'required|in:to_do,in_progress,in_review,completed'
        ]);

        $task->update($validated);

        return back()->with('success', 'Status updated successfully!');
    }

    public function updatePriority(Request $request, Task $task)
    {
        // Validate that priority is between 1 and 5
        $validated = $request->validate([
            'priority' => 'required|integer|min:1|max:5'
        ]);

        $task->update($validated);

        return back()->with('success', 'Priority updated successfully!');
    }
}
