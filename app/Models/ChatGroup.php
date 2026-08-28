<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class)->orderBy('created_at');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class)->orderBy('meeting_time');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ChatGroupMember::class);
    }

    public function isMember(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->members()->where('user_id', $user->id)->exists();
    }
}
