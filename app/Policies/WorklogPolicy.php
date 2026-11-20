<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Worklog;

class WorklogPolicy
{
    public function view(User $user, Worklog $worklog): bool
    {
        return $worklog->user_id === $user->id;
    }

    public function update(User $user, Worklog $worklog): bool
    {
        return $worklog->user_id === $user->id;
    }

    public function delete(User $user, Worklog $worklog): bool
    {
        return $worklog->user_id === $user->id;
    }
}
