<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'title',
        'name',
        'description',
        'technologies',
        'required_skills',
        'team_size',
    ];

    /**
     * Keep title and name synchronized.
     */
    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value;

        if (empty($this->attributes['name'])) {
            $this->attributes['name'] = $value;
        }
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;

        if (empty($this->attributes['title'])) {
            $this->attributes['title'] = $value;
        }
    }

    public function getTitleAttribute($value): ?string
    {
        return $value ?? ($this->attributes['name'] ?? null);
    }

    public function getNameAttribute($value): ?string
    {
        return $value ?? ($this->attributes['title'] ?? null);
    }

    /**
     * Project tasks.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Project members.
     */
    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    /**
     * Check whether a user belongs to this project.
     */
    public function isMember(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Check whether a user is the project leader.
     */
    public function isLeader(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->members()
            ->where('user_id', $user->id)
            ->where('is_leader', true)
            ->exists();
    }
}