<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $fillable = [
        'task_id',
        'reminder_time',
        'is_sent',
    ];

    protected $casts = [
        'reminder_time' => 'datetime',
        'is_sent' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}