<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'department',
        'name',
    ];

    /**
     * Scope to filter suggestions by department.
     * Also returns 'General' interests (available to all departments).
     */
    public function scopeForDepartment($query, ?string $department)
    {
        $department = trim((string) $department);

        if (empty($department)) {
            $department = 'General';
        }

        return $query->where(function ($q) use ($department) {
            $q->whereRaw(
                'LOWER(department) = ?',
                [strtolower($department)]
            )
            ->orWhereRaw(
                'LOWER(department) = ?',
                ['general']
            );
        })->orderBy('name');
    }
}