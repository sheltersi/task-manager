<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('tasks', TaskController::class);
      Route::patch('/tasks/{task}/advance', [TaskController::class, 'advance'])
    ->name('tasks.regress');

    Route::patch('/tasks/{task}/regress', [TaskController::class, 'regress'])
    ->name('tasks.regress');

});
