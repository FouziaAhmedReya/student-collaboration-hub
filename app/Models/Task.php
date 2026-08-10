<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Task extends Model
{
    protected $fillable = [
        'project_id',
        'task_name',
        'assigned_to',
        'deadline',
        'status',
        'google_calendar_event_id',
    ];
 
    protected $casts = [
        'deadline' => 'datetime',
    ];
 
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
 
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
 




















