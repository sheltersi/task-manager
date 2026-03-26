<?php

use App\Http\Controllers\TaskController;
use App\Livewire\SharedTaskList;
use App\Livewire\TaskForm;
use App\Livewire\TaskList;
use App\Livewire\TaskShow;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');

    // Route::resource('tasks', TaskController::class)->except('index');
    // Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    // Route::patch('/tasks/{task}/priority', [TaskController::class, 'updatePriority'])->name('tasks.update-priority');

    Route::get('/dashboard', TaskList::class)->name('dashboard');
Route::get('/tasks/create', TaskForm::class)->name('tasks.create');
Route::get('/tasks/{task}/edit', TaskForm::class)->name('tasks.edit');
Route::get('/tasks/{task}', TaskShow::class)->name('tasks.show');
Route::get('/shared', SharedTaskList::class)->name('shared.tasks');
});
