<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    /**
     * Book-purchase requests made by this student.
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
     * Requested resources uploaded by this student or tutor.
     */
    public function resourceUploads()
    {
        return $this->hasMany(ResourceUpload::class);
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