<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIdea extends Model
{
    use HasFactory;

    protected $table = 'ideas';

    protected $fillable = [
        'title',
        'description',
        'domain',
        'tech_stack',
    ];
}
