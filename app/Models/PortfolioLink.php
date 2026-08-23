<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'title',
        'platform',
        'url',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function getDisplayTitleAttribute(): string
    {
        if (!empty($this->title) && !empty($this->platform) && $this->title !== $this->platform) {
            return "{$this->platform} ({$this->title})";
        }
        return $this->title ?: ($this->platform ?: 'Portfolio Link');
    }
}
