<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorMaterial extends Model
{
    protected $fillable = [
        'tutor_id',
        'title',
        'file_name',
        'file_url',
        'cloudinary_public_id',
        'resource_type',
    ];


    /*
    |--------------------------------------------------------------------------
    | Tutor
    |--------------------------------------------------------------------------
    */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(
            Tutor::class
        );
    }
}