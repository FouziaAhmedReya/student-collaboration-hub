<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'requester_name',
        'course_code',
        'course_name',
        'title',
        'description',
        'status',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(
            ResourceUpload::class
        );
    }
}