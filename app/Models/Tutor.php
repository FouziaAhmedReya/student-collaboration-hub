<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutor extends Model
{
    protected $fillable = [
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


    public function materials(): HasMany
    {
        return $this->hasMany(
            TutorMaterial::class
        );
    }
}
