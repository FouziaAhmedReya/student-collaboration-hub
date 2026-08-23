<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'name',
        'proficiency',
        'proficiency_level',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'proficiency' => 'integer',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public static function proficiencyLevelFromInt(int $val): string
    {
        return match (true) {
            $val >= 85 => 'Expert',
            $val >= 60 => 'Advanced',
            $val >= 35 => 'Intermediate',
            default => 'Beginner',
        };
    }

    public static function intFromProficiencyLevel(string $level): int
    {
        return match (strtolower($level)) {
            'expert' => 95,
            'advanced' => 75,
            'intermediate' => 50,
            'beginner' => 25,
            default => 50,
        };
    }
}
