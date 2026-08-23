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
        'meeting_time',
        'deadline',
        'google_calendar_event_id',
    ];

    protected function casts(): array
    {
        return [
            'meeting_time' => 'datetime',
            'deadline' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class, 'meeting_id');
    }
}