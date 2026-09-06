<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Attributes that can be saved using create() or update().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'student_id',
        'department',
        'phone',
    ];

    /**
     * Attributes hidden when the user is converted to an array or JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * User profile relationship.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }


    /**
     * Notes uploaded by this student.
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }


    /**
     * Book listings created by this student.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function booksForSale()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Book purchase requests made by this student.
     */
    public function bookOrders()
    {
        return $this->hasMany(
            BookOrder::class,
            'buyer_id'
        );
    }


    /**
     * Tutor Finder profile belonging to this tutor.
     */
    public function tutorProfile()
    {
        return $this->hasOne(Tutor::class);
    }


    /**
     * Tutor ratings submitted by this student.
     */
    public function tutorRatings()
    {
        return $this->hasMany(TutorRating::class);
    }


    /**
     * Resource requests created by this student.
     */
    public function resourceRequests()
    {
        return $this->hasMany(ResourceRequest::class);
    }


    /**
     * Resource uploads created by this student or tutor.
     */
    public function resourceUploads()
    {
        return $this->hasMany(ResourceUpload::class);
    }



    /*
    |--------------------------------------------------------------------------
    | Study Group Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Study groups created by this user.
     */
    public function createdStudyGroups(): HasMany
    {
        return $this->hasMany(
            StudyGroup::class,
            'creator_id'
        );
    }


    /**
     * Study group membership records.
     */
    public function studyGroupMemberships(): HasMany
    {
        return $this->hasMany(
            StudyGroupMember::class
        );
    }


    /**
     * Study groups joined by this user.
     */
    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            StudyGroup::class,
            'study_group_members'
        )
        ->withPivot([
            'id',
            'role',
            'status',
            'joined_at'
        ])
        ->withTimestamps();
    }



    /*
    |--------------------------------------------------------------------------
    | Project Team Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Project recruitments created by this user.
     */
    public function createdProjectRecruitments(): HasMany
    {
        return $this->hasMany(
            ProjectRecruitment::class,
            'creator_id'
        );
    }


    /**
     * Project team memberships.
     */
    public function projectTeamMemberships(): HasMany
    {
        return $this->hasMany(
            ProjectTeamMember::class,
            'user_id'
        );
    }


    /**
     * Projects joined by this user.
     */
    public function joinedProjects(): BelongsToMany
    {
        return $this->belongsToMany(
            ProjectRecruitment::class,
            'project_team_members',
            'user_id',
            'project_recruitment_id'
        )
        ->withPivot([
            'id',
            'role',
            'status',
            'joined_at'
        ])
        ->withTimestamps();
    }



    /**
     * Attribute type conversions.
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
}