<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'availability',
        'rating',
        'bio',
        'profile_image_url',
        'profile_image_public_id',
        'profile_image_resource_type',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(TutorMaterial::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(TutorRating::class);
    }

    public function refreshAverageRating(): void
    {
        $average = $this->ratings()
            ->avg('rating');

        $this->update([
            'rating' => round(
                (float) ($average ?? 0),
                1
            ),
        ]);
    }
}