<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'department',
        'course',
        'semester',
        'original_name',
        'public_id',
        'secure_url',
        'resource_type',
        'format',
        'mime_type',
        'bytes',
        'downloads_count',
    ];

    protected function casts(): array
    {
        return [
            'bytes' => 'integer',
            'downloads_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileSizeAttribute(): string
    {
        if ($this->bytes < 1024) {
            return $this->bytes.' B';
        }

        if ($this->bytes < 1024 * 1024) {
            return number_format($this->bytes / 1024, 1).' KB';
        }

        return number_format($this->bytes / (1024 * 1024), 1).' MB';
    }
}
