<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    public const CONDITIONS = [
        'new' => 'New',
        'like_new' => 'Like New',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
    ];

    public const CATEGORIES = [
        'Textbook',
        'Reference',
        'Programming',
        'Engineering',
        'Business',
        'Science',
        'Mathematics',
        'Literature',
        'Admission & Job Prep',
        'Other',
    ];

    protected $fillable = [
        'user_id',
        'owner_token',
        'title',
        'author',
        'price',
        'course',
        'category',
        'condition',
        'description',
        'status',
        'seller_name',
        'seller_email',
        'seller_phone',
        'original_image_name',
        'image_public_id',
        'image_url',
        'image_resource_type',
        'image_format',
        'image_mime_type',
        'image_bytes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'image_bytes' => 'integer',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(BookOrder::class);
    }

    public function getConditionLabelAttribute(): string
    {
        return self::CONDITIONS[$this->condition]
            ?? ucfirst(str_replace('_', ' ', $this->condition));
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Available',
            'reserved' => 'Reserved',
            'sold' => 'Sold',
            default => ucfirst($this->status),
        };
    }
}