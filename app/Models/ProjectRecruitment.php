<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectRecruitment extends Model
{
    use HasFactory;

    protected $table = 'project_recruitments';

    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'course',
        'project_type',
        'required_members',
        'current_members',
        'required_skills',
        'recruitment_status',
        'meeting_date',
        'meeting_time',
        'location_name',
        'location_address',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date'     => 'date',
            'required_members' => 'integer',
            'current_members'  => 'integer',
            'latitude'         => 'float',
            'longitude'        => 'float',
        ];
    }

    /**
     * Get the student who created the recruitment post.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get all team membership/request records.
     */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(ProjectTeamMember::class, 'project_recruitment_id');
    }

    /**
     * Get active team members.
     */
    public function activeMembers(): HasMany
    {
        return $this->hasMany(ProjectTeamMember::class, 'project_recruitment_id')
            ->where('status', 'active');
    }

    /**
     * Get pending join requests.
     */
    public function pendingRequests(): HasMany
    {
        return $this->hasMany(ProjectTeamMember::class, 'project_recruitment_id')
            ->where('status', 'pending');
    }

    /**
     * Get users in this project team.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_team_members', 'project_recruitment_id', 'user_id')
            ->withPivot(['id', 'role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Check if a given user is the creator of this recruitment post.
     */
    public function isCreator(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return (int) $this->creator_id === (int) $user->id;
    }

    /**
     * Check if a given user is an active member of this project team.
     */
    public function isMember(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->teamMemberships()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Check if a given user has a pending join request.
     */
    public function isPending(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->teamMemberships()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Get the membership record for a given user.
     */
    public function getMembershipFor(?User $user): ?ProjectTeamMember
    {
        if (!$user) {
            return null;
        }

        return $this->teamMemberships()
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Check if the recruitment post is currently open.
     */
    public function isOpen(): bool
    {
        return $this->recruitment_status === 'open';
    }

    /**
     * Get skills as an array.
     */
    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->required_skills)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->required_skills))));
    }

    /**
     * Check if project team has reached maximum capacity.
     */
    public function hasReachedMaxMembers(): bool
    {
        return $this->current_members >= $this->required_members;
    }

    /**
     * Alias for hasReachedMaxMembers.
     */
    public function isFull(): bool
    {
        return $this->hasReachedMaxMembers();
    }

    /**
     * Recalculate and update the current_members count from active memberships.
     */
    public function syncCurrentMembers(): void
    {
        $count = $this->teamMemberships()->where('status', 'active')->count();
        $this->update(['current_members' => max(1, $count)]);
    }
}
