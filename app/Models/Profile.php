<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_photo',
        'department',
        'semester',
        'university',
        'phone',
        'joined_date',
        'about_me',
        'bio',
        'preferred_location_name',
        'preferred_location_address',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

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

    public function studentProjects(): HasMany
    {
        return $this->hasMany(StudentProject::class);
    }

    public function portfolioLinks(): HasMany
    {
        return $this->hasMany(PortfolioLink::class);
    }

    /**
     * Calculate profile completion percentage dynamically.
     */
    public function getCompletionPercentageAttribute(): int
    {
        $details = $this->completion_details;
        $total = 0;

        foreach ($details as $section) {
            if ($section['completed']) {
                $total += $section['weight'];
            }
        }

        return min(100, $total);
    }

    /**
     * Get breakdown of profile completion components.
     */
    public function getCompletionDetailsAttribute(): array
    {
        $hasSkills = $this->relationLoaded('skills')
            ? $this->skills->isNotEmpty()
            : $this->skills()->exists();

        $hasInterests = $this->relationLoaded('interests')
            ? $this->interests->isNotEmpty()
            : $this->interests()->exists();

        $hasProjects = $this->relationLoaded('studentProjects')
            ? $this->studentProjects->isNotEmpty()
            : $this->studentProjects()->exists();

        $hasLinks = $this->relationLoaded('portfolioLinks')
            ? $this->portfolioLinks->isNotEmpty()
            : $this->portfolioLinks()->exists();

        return [
            'department' => [
                'label' => 'Department',
                'completed' => !empty(trim((string) $this->department)),
                'weight' => 15,
            ],
            'semester' => [
                'label' => 'Semester',
                'completed' => !empty(trim((string) $this->semester)),
                'weight' => 10,
            ],
            'bio' => [
                'label' => 'Bio / About Me',
                'completed' => !empty(trim((string) ($this->bio ?: $this->about_me))),
                'weight' => 10,
            ],
            'phone' => [
                'label' => 'Phone / Contact',
                'completed' => !empty(trim((string) $this->phone)),
                'weight' => 5,
            ],
            'skills' => [
                'label' => 'Technical Skills',
                'completed' => $hasSkills,
                'weight' => 20,
            ],
            'interests' => [
                'label' => 'Academic & Career Interests',
                'completed' => $hasInterests,
                'weight' => 10,
            ],
            'projects' => [
                'label' => 'Completed Projects',
                'completed' => $hasProjects,
                'weight' => 15,
            ],
            'portfolio' => [
                'label' => 'Portfolio Links',
                'completed' => $hasLinks,
                'weight' => 10,
            ],
            'location' => [
                'label' => 'Preferred Study Location',
                'completed' => !is_null($this->latitude) && !is_null($this->longitude),
                'weight' => 5,
            ],
        ];
    }
}
