<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProject extends Model
{
    use HasFactory;

    protected $table = 'student_projects';

    protected $fillable = [
        'profile_id',
        'title',
        'description',
        'technologies',
        'project_url',
        'repo_url',
        'completed_date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
