<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'required_skills',
        'team_size',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function isMember(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function isLeader(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->members()->where('user_id', $user->id)->where('is_leader', true)->exists();
    }
}
