<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'course',
        'max_members',
        'meeting_date',
        'meeting_time',
        'visibility',
        'location_name',
        'location_address',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'max_members' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * The user who created the group.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * All membership pivot records.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(StudyGroupMember::class, 'study_group_id');
    }

    /**
     * All users belonging to the group (active or pending).
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'study_group_members')
            ->withPivot(['id', 'role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Active members only.
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'active');
    }

    /**
     * Pending members only.
     */
    public function pendingMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    /**
     * Admin members only.
     */
    public function admins(): BelongsToMany
    {
        return $this->members()
            ->wherePivot('role', 'admin')
            ->wherePivot('status', 'active');
    }

    /**
     * Check if a specific user is the original creator.
     */
    public function isCreator(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return (int) $this->creator_id === (int) $user->id;
    }

    /**
     * Check if a specific user is an admin of the group.
     */
    public function isAdmin(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->isCreator($user)) {
            return true;
        }

        return $this->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Check if a user is an active member.
     */
    public function isMember(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Check if a user has a pending join request.
     */
    public function isPending(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Get active member count.
     */
    public function activeMembersCount(): int
    {
        return $this->memberships()->where('status', 'active')->count();
    }

    /**
     * Check if the group has reached max capacity.
     */
    public function hasReachedMaxMembers(): bool
    {
        return $this->activeMembersCount() >= $this->max_members;
    }
}
