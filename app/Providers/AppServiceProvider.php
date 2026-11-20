<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\Worklog;
use App\Policies\TaskPolicy;
use App\Policies\WorklogPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Worklog::class, WorklogPolicy::class);
    }
}
