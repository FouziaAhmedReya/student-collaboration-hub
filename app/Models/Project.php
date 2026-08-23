<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'required_skills',
        'team_size',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
