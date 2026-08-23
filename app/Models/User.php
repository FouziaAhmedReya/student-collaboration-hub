<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function createdStudyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'creator_id');
    }

    public function studyGroupMemberships(): HasMany
    {
        return $this->hasMany(StudyGroupMember::class, 'user_id');
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_members')
            ->withPivot(['id', 'role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    public function createdProjectRecruitments(): HasMany
    {
        return $this->hasMany(ProjectRecruitment::class, 'creator_id');
    }

    public function projectTeamMemberships(): HasMany
    {
        return $this->hasMany(ProjectTeamMember::class, 'user_id');
    }

    public function joinedProjects(): BelongsToMany
    {
        return $this->belongsToMany(ProjectRecruitment::class, 'project_team_members', 'user_id', 'project_recruitment_id')
            ->withPivot(['id', 'role', 'status', 'joined_at'])
            ->withTimestamps();
    }
}
