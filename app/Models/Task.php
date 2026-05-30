<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'developer_id',
        'title',
        'description',
        'status',
        'progress',
        'deadline',
        'estimated_hours',
        'actual_hours',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(TaskActivityLog::class);
    }
}
