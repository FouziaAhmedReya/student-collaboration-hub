<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceUpload extends Model
{
    protected $fillable = [
        'resource_request_id',
        'user_id',
        'uploader_name',
        'title',
        'file_name',
        'file_url',
        'cloudinary_public_id',
        'resource_type',
    ];

    public function resourceRequest(): BelongsTo
    {
        return $this->belongsTo(
            ResourceRequest::class
        );
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}