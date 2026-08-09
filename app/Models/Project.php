<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'technologies', 'profile_id'])]
class Project extends Model
{
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
