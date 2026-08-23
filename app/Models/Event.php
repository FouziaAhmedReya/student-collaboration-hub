<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'description',
        'target_skills',
        'event_date',
        'location',
        'organizer',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }
}
