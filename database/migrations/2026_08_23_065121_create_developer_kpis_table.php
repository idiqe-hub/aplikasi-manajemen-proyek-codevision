<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk menyimpan KPI (Key Performance Indicator) bulanan per developer.
     *
     * Formula KPI Score (0–100):
     *   40% → on_time_rate    (task selesai tepat waktu / total task done × 100)
     *   35% → efficiency_rate (estimated_hours / actual_hours × 100, capped 100)
     *   25% → productivity    (tasks_done / target_tasks × 100, capped 100)
     */
    public function up(): void
    {
        Schema::create('developer_kpis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('developer_id')
                  ->constrained('developers')
                  ->cascadeOnDelete();

            // Format: YYYY-MM (contoh: 2026-05)
            $table->string('period_month', 7);

            // Statistik task
            $table->unsignedSmallInteger('tasks_assigned')->default(0);
            $table->unsignedSmallInteger('tasks_done')->default(0);
            $table->unsignedSmallInteger('tasks_ontime')->default(0);
            $table->unsignedSmallInteger('tasks_overdue_done')->default(0);

            // Jam kerja
            $table->decimal('total_estimated_hours', 8, 2)->default(0);
            $table->decimal('total_actual_hours', 8, 2)->default(0);

            // Komponen skor (0–100 masing-masing)
            $table->decimal('on_time_rate', 5, 2)->default(0);       // 40% weight
            $table->decimal('efficiency_rate', 5, 2)->default(0);    // 35% weight
            $table->decimal('productivity_rate', 5, 2)->default(0);  // 25% weight

            // Skor akhir KPI
            $table->decimal('kpi_score', 5, 2)->default(0);
            $table->char('grade', 1)->default('D'); // A/B/C/D

            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu record KPI per developer per bulan
            $table->unique(['developer_id', 'period_month']);
            $table->index(['period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_kpis');
    }
};
