<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceRequest extends Model
{
    protected $fillable = [
        'requester_name',
        'course_code',
        'course_name',
        'title',
        'description',
        'status',
    ];


    public function uploads(): HasMany
    {
        return $this->hasMany(
            ResourceUpload::class
        );
    }
}