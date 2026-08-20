<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah index performa untuk query filtering di laporan.
 *
 * Index baru:
 *  - projects.status                              → digunakan di WHERE filter laporan
 *  - task_activity_logs (task_id, new_status)     → digunakan di JOIN subquery weeklyProductivity
 */
return new class extends Migration
{
    public function up(): void
    {
        // Index pada tabel projects: kolom status sering difilter di laporan
        Schema::table('projects', function (Blueprint $table) {
            $table->index('status', 'idx_projects_status');
        });

        // Composite index pada task_activity_logs: task_id + new_status
        // digunakan di subquery LEFT JOIN pada weeklyProductivity
        Schema::table('task_activity_logs', function (Blueprint $table) {
            $table->index(['task_id', 'new_status'], 'idx_tal_task_id_new_status');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_status');
        });

        Schema::table('task_activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_tal_task_id_new_status');
        });
    }
};
