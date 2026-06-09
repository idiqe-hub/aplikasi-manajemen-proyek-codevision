<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'company',
    ];

    /**
     * Setiap client terhubung ke satu akun user (role = 'client').
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Satu client bisa punya banyak project.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
