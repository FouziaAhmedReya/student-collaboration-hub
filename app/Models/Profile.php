<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id',
    'profile_photo',
    'department',
    'semester',
    'university',
    'joined_date',
    'about_me',
    'preferred_location_name',
    'preferred_location_address',
    'latitude',
    'longitude'
])]
class Profile extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function interests(): HasMany
    {
        return $this->hasMany(Interest::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function portfolioLinks(): HasMany
    {
        return $this->hasMany(PortfolioLink::class);
    }

    public function getCompletionPercentage(): int
    {
        // Profile Completion = average proficiency of the student's technical skills.
        // If no skills exist, return 0 to avoid division by zero.
        $skills = $this->skills;

        if ($skills->isEmpty()) {
            return 0;
        }

        return (int) round($skills->avg('proficiency'));
    }
}
