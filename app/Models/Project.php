<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'client_name',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Project dimiliki oleh satu client.
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }
}
