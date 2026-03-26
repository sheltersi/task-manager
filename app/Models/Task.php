<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'image_path',
    ];

    protected $casts = [
        'due_date' => 'date',
        'priority' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'to_do' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
            'in_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    public function shares()
    {
        return $this->hasMany(TaskShare::class);
    }

    public function collaborators()
    {
        return $this->hasManyThrough(User::class, TaskShare::class, 'task_id', 'id', 'id', 'user_id');
    }

    // Helper to check if a user has access
    public function sharedWith(User $user): bool
    {
        return $this->shares()->where('user_id', $user->id)->exists();
    }

    // Helper to check if a user can comment
    public function canComment(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        return $this->shares()
            ->where('user_id', $user->id)
            ->where('role', 'commenter')
            ->exists();
    }
}
