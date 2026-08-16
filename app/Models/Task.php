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
        'deadline',
        'status',
        'notify_at',
        'google_calendar_event_id',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'notify_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'completed'
            && $this->deadline !== null
            && $this->deadline->isPast();
    }
}
