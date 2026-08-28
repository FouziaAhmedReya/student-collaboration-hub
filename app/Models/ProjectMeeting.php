<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMeeting extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'agenda',
        'organizer',
        'created_by_id',
        'meeting_time',
        'deadline',
        'google_calendar_event_id',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'meeting_time' => 'datetime',
            'deadline' => 'date',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class, 'meeting_id');
    }
}
