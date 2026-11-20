<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Worklog extends Model
{
    /** @use HasFactory<\Database\Factories\WorklogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'work_date',
        'hours',
        'started_at',
        'description',
        'billable',
    ];

    protected $casts = [
        'work_date' => 'date',
        'hours' => 'float',
        'billable' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
