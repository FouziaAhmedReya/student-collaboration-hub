<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['department', 'name'])]
class DepartmentInterest extends Model
{
    /**
     * Scope to filter suggestions by department.
     * Also returns 'General' interests (available to all departments).
     */
    public function scopeForDepartment($query, ?string $department)
    {
        $department = trim((string)$department);
        if (empty($department)) {
            $department = 'General';
        }

        return $query->where(function ($q) use ($department) {
            $q->whereRaw('LOWER(department) = ?', [strtolower($department)])
              ->orWhereRaw('LOWER(department) = ?', ['general']);
        })->orderBy('name');
    }
}
