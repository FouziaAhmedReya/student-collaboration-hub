<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        return $this->hasMany(StudyGroupMember::class);
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_members');
    }

    public function booksForSale(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function bookOrders(): HasMany
    {
        return $this->hasMany(BookOrder::class, 'buyer_id');
    }

    // Module 3 Feature 2 - Project Team Finder
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