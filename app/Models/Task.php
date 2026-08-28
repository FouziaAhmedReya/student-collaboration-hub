<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'assigned_user_id',
        'created_by_id',
        'deadline',
        'status',
        'notify_at',
        'reminder_sent_at',
        'google_calendar_event_id',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'notify_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'completed'
            && $this->deadline !== null
            && $this->deadline->isPast();
    }
}
