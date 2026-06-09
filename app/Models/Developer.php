<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'role',
        'skill',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // ----------------------------------------------------------------
    // Helper Workload — digunakan di capacity page dan form task
    // ----------------------------------------------------------------

    /**
     * Hitung jumlah task aktif (todo + in_progress) developer ini.
     * Gunakan setelah withCount('tasks as active_count') atau langsung query.
     */
    public function getActiveTaskCount(): int
    {
        return $this->tasks()
            ->whereIn('status', ['todo', 'in_progress'])
            ->count();
    }

    /**
     * Tentukan status workload berdasarkan jumlah task aktif.
     *   0–3 → ringan | 4–6 → normal | 7+ → overload
     */
    public static function workloadStatus(int $activeCount): string
    {
        return match (true) {
            $activeCount >= 7 => 'overload',
            $activeCount >= 4 => 'normal',
            default            => 'ringan',
        };
    }

    /**
     * Kembalikan label dan class Bootstrap badge untuk workload.
     */
    public static function workloadBadge(int $activeCount): array
    {
        return match (self::workloadStatus($activeCount)) {
            'overload' => ['label' => 'Beban Berlebih',  'class' => 'badge-danger',   'icon' => 'fa-fire'],
            'normal'   => ['label' => 'Normal',    'class' => 'badge-warning',  'icon' => 'fa-minus-circle'],
            default    => ['label' => 'Ringan',    'class' => 'badge-success',  'icon' => 'fa-check-circle'],
        };
    }
}
