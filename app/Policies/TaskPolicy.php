<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * A user can update a task only if they own it.
     */
    public function update(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * A user can delete a task only if they own it.
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * User can view task only if they own it.
     */
    public function view(User $user, Task $task): bool
    {
        if ($task->user_id === $user->id) return true;

        return $task->shares()->where('user_id', $user->id)->exists();
    }

    public function comment(User $user, Task $task): bool
    {
        return $task->canComment($user);
    }

    public function share(User $user, Task $task): bool
    {
        // only the owner can share
        return $task->user_id === $user->id;
    }
}
