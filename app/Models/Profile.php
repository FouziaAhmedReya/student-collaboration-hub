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
        $points = 0;
        $total = 10; // 10 criteria

        if ($this->user && $this->user->name) $points++;
        if ($this->profile_photo) $points++;
        if ($this->department) $points++;
        if ($this->semester) $points++;
        if ($this->university) $points++;
        if ($this->about_me) $points++;
        if ($this->skills()->exists()) $points++;
        if ($this->interests()->exists()) $points++;
        if ($this->projects()->exists()) $points++;
        if ($this->portfolioLinks()->exists()) $points++;
        if ($this->latitude !== null) $points++; // Note: using 11 criteria here based on prompt

        $totalCriteria = 11;
        return (int) round(($points / $totalCriteria) * 100);
    }
}
