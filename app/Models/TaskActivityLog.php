<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'old_status',
        'new_status',
        'old_progress',
        'new_progress',
        'note',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
