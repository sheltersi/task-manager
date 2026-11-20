<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimesheetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');

    Route::resource('tasks', TaskController::class)->except('index');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::patch('/tasks/{task}/priority', [TaskController::class, 'updatePriority'])->name('tasks.update-priority');

    // Timesheets (Tempo-style weekly matrix, timeline & CSV export)
    Route::get('/timesheets', [TimesheetController::class, 'index'])->name('timesheets.index');
    Route::get('/timesheets/export', [TimesheetController::class, 'export'])->name('timesheets.export');
    Route::post('/timesheets/worklogs', [TimesheetController::class, 'store'])->name('timesheets.worklogs.store');
    Route::put('/timesheets/worklogs/{worklog}', [TimesheetController::class, 'update'])->name('timesheets.worklogs.update');
    Route::delete('/timesheets/worklogs/{worklog}', [TimesheetController::class, 'destroy'])->name('timesheets.worklogs.destroy');
});
