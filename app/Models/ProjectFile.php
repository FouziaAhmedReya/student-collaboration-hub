<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    protected $fillable = [
        'project_id',
        'meeting_id',
        'task_id',
        'uploaded_by',
        'original_name',
        'cloudinary_public_id',
        'secure_url',
        'resource_type',
        'bytes',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(ProjectMeeting::class, 'meeting_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}