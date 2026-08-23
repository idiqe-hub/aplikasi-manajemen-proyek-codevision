<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperKpi extends Model
{
    use HasFactory;

    protected $table = 'developer_kpis';

    protected $fillable = [
        'developer_id',
        'period_month',
        'tasks_assigned',
        'tasks_done',
        'tasks_ontime',
        'tasks_overdue_done',
        'total_estimated_hours',
        'total_actual_hours',
        'on_time_rate',
        'efficiency_rate',
        'productivity_rate',
        'kpi_score',
        'grade',
        'notes',
    ];

    protected $casts = [
        'on_time_rate'      => 'float',
        'efficiency_rate'   => 'float',
        'productivity_rate' => 'float',
        'kpi_score'         => 'float',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    // ─── Helper: Kalkulasi KPI dari data mentah ───────────────────────────────

    /**
     * Hitung skor KPI dari komponen yang sudah ada.
     *
     * Formula:
     *   on_time_rate      = tasks_ontime / tasks_done × 100  (atau 100 jika 0 task done)
     *   efficiency_rate   = estimated_hours / actual_hours × 100  (capped 100, atau 100 jika tak ada jam)
     *   productivity_rate = tasks_done / max(1, tasks_assigned) × 100 (capped 100)
     *
     *   kpi_score = (on_time_rate × 0.40) + (efficiency_rate × 0.35) + (productivity_rate × 0.25)
     */
    public static function calculateScore(
        int $tasksDone,
        int $tasksOntime,
        int $tasksAssigned,
        float $estimatedHours,
        float $actualHours
    ): array {
        // Komponen 1: On-time rate (40%)
        $onTimeRate = $tasksDone > 0
            ? min(100, ($tasksOntime / $tasksDone) * 100)
            : 100.0;

        // Komponen 2: Efficiency rate (35%)
        // Efisiensi = Jika actual < estimated → lebih efisien (maks 100)
        // Jika actual > estimated → kurang efisien
        $efficiencyRate = 100.0;
        if ($actualHours > 0 && $estimatedHours > 0) {
            $efficiencyRate = min(100, ($estimatedHours / $actualHours) * 100);
        }

        // Komponen 3: Productivity rate (25%)
        $productivityRate = $tasksAssigned > 0
            ? min(100, ($tasksDone / $tasksAssigned) * 100)
            : 100.0;

        // Skor akhir
        $kpiScore = ($onTimeRate * 0.40)
                  + ($efficiencyRate * 0.35)
                  + ($productivityRate * 0.25);

        $grade = self::scoreToGrade($kpiScore);

        return [
            'on_time_rate'      => round($onTimeRate, 2),
            'efficiency_rate'   => round($efficiencyRate, 2),
            'productivity_rate' => round($productivityRate, 2),
            'kpi_score'         => round($kpiScore, 2),
            'grade'             => $grade,
        ];
    }

    /**
     * Konversi skor numerik ke grade huruf.
     * A = 85–100, B = 70–84, C = 55–69, D = < 55
     */
    public static function scoreToGrade(float $score): string
    {
        return match (true) {
            $score >= 85 => 'A',
            $score >= 70 => 'B',
            $score >= 55 => 'C',
            default      => 'D',
        };
    }

    /**
     * Kembalikan class badge Bootstrap berdasarkan grade.
     */
    public static function gradeBadgeClass(string $grade): string
    {
        return match ($grade) {
            'A'     => 'badge-success',
            'B'     => 'badge-primary',
            'C'     => 'badge-warning',
            default => 'badge-danger',
        };
    }

    /**
     * Kembalikan label teks berdasarkan grade.
     */
    public static function gradeLabel(string $grade): string
    {
        return match ($grade) {
            'A'     => 'Sangat Baik',
            'B'     => 'Baik',
            'C'     => 'Cukup',
            default => 'Perlu Perbaikan',
        };
    }
}
