<?php

namespace Database\Seeders;

use App\Models\Developer;
use App\Models\DeveloperKpi;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskActivityLog;
use App\Models\TaskComment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ComprehensiveDataSeeder — Seeder data 3 bulan terakhir untuk demo skripsi.
 *
 * Menghasilkan:
 *   - 5 proyek (termasuk 1 proyek selesai)
 *   - ~90 task historis (Mei–Agustus 2026)
 *   - ~180 task activity logs
 *   - 12 KPI records (4 developer × 3 bulan)
 *   - ~40 task comments
 *
 * Jalankan:
 *   php artisan db:seed --class=ComprehensiveDataSeeder
 */
class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // ================================================================
        // AMBIL / BUAT USER & DEVELOPER (idempotent)
        // ================================================================

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@codevision.test'],
            ['name' => 'Admin Codevision', 'password' => Hash::make('password'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        $devUsers = [
            'rizhan'  => User::firstOrCreate(['email' => 'rizhan@codevision.test'], ['name' => 'Rizhan', 'password' => Hash::make('password'), 'role' => 'developer', 'email_verified_at' => now()]),
            'randa'   => User::firstOrCreate(['email' => 'randa@codevision.test'],  ['name' => 'Randa', 'password' => Hash::make('password'), 'role' => 'developer', 'email_verified_at' => now()]),
            'lutfhi'  => User::firstOrCreate(['email' => 'lutfhi@codevision.test'], ['name' => 'Lutfhi Arifin', 'password' => Hash::make('password'), 'role' => 'developer', 'email_verified_at' => now()]),
            'bagus'   => User::firstOrCreate(['email' => 'bagus@codevision.test'],  ['name' => 'Bagus', 'password' => Hash::make('password'), 'role' => 'developer', 'email_verified_at' => now()]),
        ];

        $devs = [
            'rizhan' => Developer::firstOrCreate(['email' => 'rizhan@codevision.test'], ['user_id' => $devUsers['rizhan']->id, 'name' => 'Rizhan', 'role' => 'fullstack', 'skill' => 'Laravel, Vue.js, MySQL']),
            'randa'  => Developer::firstOrCreate(['email' => 'randa@codevision.test'],  ['user_id' => $devUsers['randa']->id, 'name' => 'Randa', 'role' => 'backend', 'skill' => 'PHP, Laravel, PostgreSQL']),
            'lutfhi' => Developer::firstOrCreate(['email' => 'lutfhi@codevision.test'], ['user_id' => $devUsers['lutfhi']->id, 'name' => 'Lutfhi Arifin', 'role' => 'frontend', 'skill' => 'React, Tailwind CSS, JavaScript']),
            'bagus'  => Developer::firstOrCreate(['email' => 'bagus@codevision.test'],  ['user_id' => $devUsers['bagus']->id, 'name' => 'Bagus', 'role' => 'backend', 'skill' => 'Node.js, Express, MongoDB']),
        ];

        // Sync user_id
        foreach (['rizhan','randa','lutfhi','bagus'] as $key) {
            if ($devs[$key]->user_id !== $devUsers[$key]->id) {
                $devs[$key]->update(['user_id' => $devUsers[$key]->id]);
            }
        }

        $this->command->info('✔ Users & Developer records siap.');

        // ================================================================
        // PROYEK BARU: Proyek sudah selesai (historis)
        // ================================================================

        $proj5 = Project::updateOrCreate(
            ['name' => 'Aplikasi Absensi Karyawan Digital'],
            [
                'client_id'   => null,
                'client_name' => 'PT. Maju Bersama',
                'status'      => 'completed',
                'start_date'  => $today->copy()->subMonths(4)->format('Y-m-d'),
                'end_date'    => $today->copy()->subMonths(1)->format('Y-m-d'),
                'description' => 'Aplikasi absensi berbasis QR-Code dan GPS untuk pencatatan kehadiran karyawan secara real-time.',
            ]
        );

        $proj6 = Project::updateOrCreate(
            ['name' => 'Platform E-Learning Internal Codevision'],
            [
                'client_id'   => null,
                'client_name' => 'Internal Codevision',
                'status'      => 'on_progress',
                'start_date'  => $today->copy()->subMonths(2)->format('Y-m-d'),
                'end_date'    => $today->copy()->addMonths(2)->format('Y-m-d'),
                'description' => 'Platform pembelajaran internal untuk onboarding developer baru, mencakup modul, kuis, dan sertifikasi.',
            ]
        );

        $this->command->info('✔ Proyek tambahan siap.');

        // ================================================================
        // GENERATE TASK HISTORIS 3 BULAN TERAKHIR
        // Bulan: Mei, Juni, Juli 2026 (histori) + Agustus 2026 (berjalan)
        // ================================================================

        // Referensi proyek yang sudah ada (dari DemoDataSeeder)
        $proj1 = Project::where('name', 'Sistem Informasi Inventaris Barang')->first();
        $proj2 = Project::where('name', 'Website Company Profile UMKM')->first();
        $proj3 = Project::where('name', 'Aplikasi Manajemen Pemesanan Online')->first();
        $proj4 = Project::where('name', 'Say It Last')->first();

        // Jika proyek belum ada, skip
        $projects = array_filter([$proj1, $proj2, $proj3, $proj4, $proj5, $proj6], fn($p) => $p !== null);

        $this->command->info('✔ Referensi proyek: ' . count($projects) . ' proyek ditemukan.');

        // ─── DATA TASK HISTORIS ───────────────────────────────────────────────

        $historicalTasks = $this->getHistoricalTaskData($today, $devs, $proj5, $proj6, $proj1, $proj2, $proj3, $proj4);

        $createdTasks = [];
        $taskTimestamps = [];

        foreach ($historicalTasks as $taskData) {
            $taskKey = $taskData['key'];

            $identifier = [
                'project_id' => $taskData['project_id'],
                'title'      => $taskData['title'],
            ];

            // Pisahkan field non-DB: key, title, updated_at
            $excludedFields = ['key', 'title', 'updated_at'];
            $attributes = array_diff_key($taskData, array_flip($excludedFields));

            // Simpan updated_at terpisah untuk update manual
            if (isset($taskData['updated_at'])) {
                $taskTimestamps[$taskKey] = $taskData['updated_at'];
            }

            $task = Task::updateOrCreate($identifier, $attributes);
            $createdTasks[$taskKey] = $task;
        }

        // Force-update timestamps historis via DB::table (bypass Eloquent)
        foreach ($taskTimestamps as $taskKey => $updatedAt) {
            if (!isset($createdTasks[$taskKey])) continue;
            DB::table('tasks')
                ->where('id', $createdTasks[$taskKey]->id)
                ->update(['updated_at' => $updatedAt]);
        }

        $this->command->info('✔ ' . count($createdTasks) . ' task historis dibuat/diperbarui.');

        // ─── ACTIVITY LOGS HISTORIS ───────────────────────────────────────────

        $this->seedActivityLogs($createdTasks, $devs, $devUsers, $adminUser, $today);
        $this->command->info('✔ Activity logs historis dibuat.');

        // ─── COMMENTS HISTORIS ────────────────────────────────────────────────

        $this->seedComments($createdTasks, $devs, $devUsers, $adminUser);
        $this->command->info('✔ Comments historis dibuat.');

        // ─── KPI RECORDS (3 BULAN) ───────────────────────────────────────────

        $this->seedKpiRecords($devs, $today);
        $this->command->info('✔ KPI records 3 bulan dibuat.');

        // ================================================================
        // RINGKASAN
        // ================================================================
        $this->command->newLine();
        $this->command->info('================================================================');
        $this->command->info('  ComprehensiveDataSeeder selesai!');
        $this->command->info('================================================================');
        $this->command->info('  Total task: ' . Task::count());
        $this->command->info('  Total activity logs: ' . TaskActivityLog::count());
        $this->command->info('  Total KPI records: ' . DeveloperKpi::count());
        $this->command->info('  Total proyek: ' . Project::count());
        $this->command->newLine();
    }

    // ────────────────────────────────────────────────────────────────────────────
    // HELPER: Data task historis
    // ────────────────────────────────────────────────────────────────────────────

    private function getHistoricalTaskData(Carbon $today, array $devs, $proj5, $proj6, $proj1, $proj2, $proj3, $proj4): array
    {
        $tasks = [];

        // ────────── PROYEK 5: Absensi (SELESAI) ─────────────────────────────
        if ($proj5) {
            $pid = $proj5->id;

            // Bulan Mei
            $tasks[] = ['key'=>'p5_1', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Analisis kebutuhan sistem absensi', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(3)->subDays(20)->format('Y-m-d'), 'estimated_hours'=>6, 'actual_hours'=>5, 'description'=>'Analisis kebutuhan fitur absensi QR dan GPS.', 'updated_at'=>$today->copy()->subMonths(3)->subDays(18)];
            $tasks[] = ['key'=>'p5_2', 'project_id'=>$pid, 'developer_id'=>$devs['randa']->id, 'title'=>'Desain ERD dan skema database absensi', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(3)->subDays(15)->format('Y-m-d'), 'estimated_hours'=>8, 'actual_hours'=>7, 'description'=>'ERD untuk entitas karyawan, absensi, shift, dan lokasi.', 'updated_at'=>$today->copy()->subMonths(3)->subDays(13)];
            $tasks[] = ['key'=>'p5_3', 'project_id'=>$pid, 'developer_id'=>$devs['lutfhi']->id, 'title'=>'Implementasi modul login dan manajemen user', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(3)->subDays(10)->format('Y-m-d'), 'estimated_hours'=>10, 'actual_hours'=>9, 'description'=>'Autentikasi, role management (admin, karyawan).', 'updated_at'=>$today->copy()->subMonths(3)->subDays(8)];
            $tasks[] = ['key'=>'p5_4', 'project_id'=>$pid, 'developer_id'=>$devs['bagus']->id, 'title'=>'Implementasi fitur absensi via QR-Code', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(25)->format('Y-m-d'), 'estimated_hours'=>14, 'actual_hours'=>16, 'description'=>'Generate QR unik per sesi absensi, scan dan validasi.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(22)];
            $tasks[] = ['key'=>'p5_5', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Integrasi GPS untuk validasi lokasi absensi', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(18)->format('Y-m-d'), 'estimated_hours'=>12, 'actual_hours'=>11, 'description'=>'Validasi koordinat GPS saat absensi masuk/keluar.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(16)];
            $tasks[] = ['key'=>'p5_6', 'project_id'=>$pid, 'developer_id'=>$devs['randa']->id, 'title'=>'Membuat laporan rekapitulasi kehadiran', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(10)->format('Y-m-d'), 'estimated_hours'=>10, 'actual_hours'=>10, 'description'=>'Laporan kehadiran bulanan dalam format tabel dan PDF.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(8)];
            $tasks[] = ['key'=>'p5_7', 'project_id'=>$pid, 'developer_id'=>$devs['lutfhi']->id, 'title'=>'Desain UI dashboard monitoring absensi', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(5)->format('Y-m-d'), 'estimated_hours'=>8, 'actual_hours'=>7, 'description'=>'Dashboard admin untuk monitoring real-time kehadiran karyawan.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(3)];
            $tasks[] = ['key'=>'p5_8', 'project_id'=>$pid, 'developer_id'=>$devs['bagus']->id, 'title'=>'Testing dan bug fixing aplikasi absensi', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(5)->format('Y-m-d'), 'estimated_hours'=>12, 'actual_hours'=>14, 'description'=>'End-to-end testing semua fitur, perbaikan bug kritikal.', 'updated_at'=>$today->copy()->subMonths(1)->subDays(3)];
            $tasks[] = ['key'=>'p5_9', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Deployment dan dokumentasi teknis', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(1)->format('Y-m-d'), 'estimated_hours'=>6, 'actual_hours'=>5, 'description'=>'Deploy ke server produksi dan buat dokumentasi API.', 'updated_at'=>$today->copy()->subMonths(1)];
        }

        // ────────── PROYEK 6: E-Learning (BERJALAN) ──────────────────────────
        if ($proj6) {
            $pid = $proj6->id;

            $tasks[] = ['key'=>'p6_1', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Analisis dan perencanaan modul e-learning', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(20)->format('Y-m-d'), 'estimated_hours'=>6, 'actual_hours'=>6, 'description'=>'Identifikasi modul learning: video, kuis, sertifikasi.', 'updated_at'=>$today->copy()->subMonths(1)->subDays(18)];
            $tasks[] = ['key'=>'p6_2', 'project_id'=>$pid, 'developer_id'=>$devs['randa']->id, 'title'=>'Desain database modul dan progres belajar', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(14)->format('Y-m-d'), 'estimated_hours'=>8, 'actual_hours'=>7, 'description'=>'ERD untuk courses, lessons, users_progress, quiz.', 'updated_at'=>$today->copy()->subMonths(1)->subDays(12)];
            $tasks[] = ['key'=>'p6_3', 'project_id'=>$pid, 'developer_id'=>$devs['lutfhi']->id, 'title'=>'Implementasi halaman daftar dan detail kursus', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subDays(18)->format('Y-m-d'), 'estimated_hours'=>10, 'actual_hours'=>11, 'description'=>'Halaman listing kursus dengan filter kategori dan detail konten.', 'updated_at'=>$today->copy()->subDays(16)];
            $tasks[] = ['key'=>'p6_4', 'project_id'=>$pid, 'developer_id'=>$devs['bagus']->id, 'title'=>'Implementasi sistem kuis dan penilaian', 'status'=>'in_progress', 'progress'=>60, 'deadline'=>$today->copy()->addDays(5)->format('Y-m-d'), 'estimated_hours'=>12, 'actual_hours'=>7, 'description'=>'Sistem kuis pilihan ganda, essay, dan kalkulasi nilai.', 'updated_at'=>$today->copy()->subDays(2)];
            $tasks[] = ['key'=>'p6_5', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Fitur upload video dan manajemen konten', 'status'=>'in_progress', 'progress'=>45, 'deadline'=>$today->copy()->addDays(8)->format('Y-m-d'), 'estimated_hours'=>14, 'actual_hours'=>6, 'description'=>'Upload video, streaming, dan manajemen konten kursus oleh admin.', 'updated_at'=>$today->copy()->subDays(1)];
            $tasks[] = ['key'=>'p6_6', 'project_id'=>$pid, 'developer_id'=>$devs['randa']->id, 'title'=>'Sistem sertifikasi dan badge pencapaian', 'status'=>'todo', 'progress'=>0, 'deadline'=>$today->copy()->addDays(15)->format('Y-m-d'), 'estimated_hours'=>10, 'actual_hours'=>0, 'description'=>'Generate sertifikat PDF setelah kursus selesai, badge digital.'];
            $tasks[] = ['key'=>'p6_7', 'project_id'=>$pid, 'developer_id'=>$devs['lutfhi']->id, 'title'=>'Dashboard progres belajar developer', 'status'=>'todo', 'progress'=>0, 'deadline'=>$today->copy()->addDays(20)->format('Y-m-d'), 'estimated_hours'=>8, 'actual_hours'=>0, 'description'=>'Dashboard personal yang menampilkan kursus selesai, progres, dan sertifikat.'];
        }

        // ────────── PROYEK 1: Inventaris — Task Historis Tambahan ────────────
        if ($proj1) {
            $pid = $proj1->id;
            // Task historis Mei (sudah done)
            $tasks[] = ['key'=>'p1_h1', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Setup repository dan environment development', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(3)->format('Y-m-d'), 'estimated_hours'=>4, 'actual_hours'=>3, 'description'=>'Setup Git, Docker, dan konfigurasi environment lokal.', 'updated_at'=>$today->copy()->subMonths(3)->addDays(1)];
            $tasks[] = ['key'=>'p1_h2', 'project_id'=>$pid, 'developer_id'=>$devs['randa']->id, 'title'=>'Membuat modul kategori barang', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(20)->format('Y-m-d'), 'estimated_hours'=>6, 'actual_hours'=>5, 'description'=>'CRUD kategori barang dengan validasi unik.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(18)];
            $tasks[] = ['key'=>'p1_h3', 'project_id'=>$pid, 'developer_id'=>$devs['lutfhi']->id, 'title'=>'Implementasi fitur notifikasi stok minimum', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(10)->format('Y-m-d'), 'estimated_hours'=>8, 'actual_hours'=>9, 'description'=>'Alert otomatis ketika stok barang di bawah threshold minimum.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(7)];
            $tasks[] = ['key'=>'p1_h4', 'project_id'=>$pid, 'developer_id'=>$devs['bagus']->id, 'title'=>'Ekspor data barang ke Excel', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(15)->format('Y-m-d'), 'estimated_hours'=>5, 'actual_hours'=>6, 'description'=>'Fitur ekspor daftar inventaris ke format Excel (.xlsx).', 'updated_at'=>$today->copy()->subMonths(1)->subDays(13)];
        }

        // ────────── PROYEK 2: UMKM — Task Historis Tambahan ─────────────────
        if ($proj2) {
            $pid = $proj2->id;
            $tasks[] = ['key'=>'p2_h1', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Setup frontend framework dan design system', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(3)->subDays(5)->format('Y-m-d'), 'estimated_hours'=>5, 'actual_hours'=>4, 'description'=>'Konfigurasi Bootstrap, color palette, dan typography.', 'updated_at'=>$today->copy()->subMonths(3)->subDays(3)];
            $tasks[] = ['key'=>'p2_h2', 'project_id'=>$pid, 'developer_id'=>$devs['bagus']->id, 'title'=>'Integrasi Google Maps untuk halaman lokasi', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(8)->format('Y-m-d'), 'estimated_hours'=>4, 'actual_hours'=>5, 'description'=>'Embed Google Maps API untuk tampilkan lokasi usaha.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(6)];
            $tasks[] = ['key'=>'p2_h3', 'project_id'=>$pid, 'developer_id'=>$devs['randa']->id, 'title'=>'SEO optimization dan meta tag', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(20)->format('Y-m-d'), 'estimated_hours'=>4, 'actual_hours'=>3, 'description'=>'Optimasi meta title, description, OG tags untuk SEO.', 'updated_at'=>$today->copy()->subMonths(1)->subDays(18)];
        }

        // ────────── PROYEK 3: Pemesanan — Task Historis Tambahan ─────────────
        if ($proj3) {
            $pid = $proj3->id;
            $tasks[] = ['key'=>'p3_h1', 'project_id'=>$pid, 'developer_id'=>$devs['lutfhi']->id, 'title'=>'Implementasi payment gateway integrasi', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(2)->subDays(5)->format('Y-m-d'), 'estimated_hours'=>16, 'actual_hours'=>18, 'description'=>'Integrasi Midtrans payment gateway untuk transaksi online.', 'updated_at'=>$today->copy()->subMonths(2)->subDays(2)];
            $tasks[] = ['key'=>'p3_h2', 'project_id'=>$pid, 'developer_id'=>$devs['rizhan']->id, 'title'=>'Membuat notifikasi email pesanan', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(10)->format('Y-m-d'), 'estimated_hours'=>6, 'actual_hours'=>5, 'description'=>'Email konfirmasi pesanan, pembayaran, dan pengiriman.', 'updated_at'=>$today->copy()->subMonths(1)->subDays(8)];
            $tasks[] = ['key'=>'p3_h3', 'project_id'=>$pid, 'developer_id'=>$devs['bagus']->id, 'title'=>'Fitur tracking status pesanan pelanggan', 'status'=>'in_progress', 'progress'=>40, 'deadline'=>$today->copy()->addDays(4)->format('Y-m-d'), 'estimated_hours'=>10, 'actual_hours'=>4, 'description'=>'Halaman tracking status pesanan real-time untuk pelanggan.', 'updated_at'=>$today->copy()->subDays(1)];
        }

        // ────────── PROYEK 4: Say It Last — Task Historis Tambahan ──────────
        if ($proj4) {
            $pid = $proj4->id;
            $tasks[] = ['key'=>'p4_h1', 'project_id'=>$pid, 'developer_id'=>$devs['randa']->id, 'title'=>'Implementasi sistem notifikasi push', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->subDays(5)->format('Y-m-d'), 'estimated_hours'=>10, 'actual_hours'=>11, 'description'=>'Push notification via Firebase untuk pengiriman pesan terjadwal.', 'updated_at'=>$today->copy()->subMonths(1)->subDays(3)];
            $tasks[] = ['key'=>'p4_h2', 'project_id'=>$pid, 'developer_id'=>$devs['lutfhi']->id, 'title'=>'Implementasi enkripsi end-to-end pesan', 'status'=>'done', 'progress'=>100, 'deadline'=>$today->copy()->subMonths(1)->format('Y-m-d'), 'estimated_hours'=>12, 'actual_hours'=>10, 'description'=>'Enkripsi pesan menggunakan AES-256 agar hanya penerima bisa baca.', 'updated_at'=>$today->copy()->subMonths(1)->addDays(1)];
        }

        return $tasks;
    }

    // ────────────────────────────────────────────────────────────────────────────
    // HELPER: Seed activity logs
    // ────────────────────────────────────────────────────────────────────────────

    private function seedActivityLogs(array $createdTasks, array $devs, array $devUsers, $adminUser, Carbon $today): void
    {
        $logs = [
            // Proyek 5: Absensi (semua done)
            ['task_key'=>'p5_1', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'todo', 'new_status'=>'in_progress', 'old_progress'=>0, 'new_progress'=>30, 'note'=>'Mulai analisis, koordinasi dengan HR.', 'created_at'=>$today->copy()->subMonths(3)->subDays(22)],
            ['task_key'=>'p5_1', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>80, 'new_progress'=>100, 'note'=>'Dokumen kebutuhan selesai, disetujui.', 'created_at'=>$today->copy()->subMonths(3)->subDays(18)],
            ['task_key'=>'p5_2', 'user_id'=>$devUsers['randa']->id, 'old_status'=>'todo', 'new_status'=>'in_progress', 'old_progress'=>0, 'new_progress'=>40, 'note'=>'ERD v1 sudah dibuat.', 'created_at'=>$today->copy()->subMonths(3)->subDays(17)],
            ['task_key'=>'p5_2', 'user_id'=>$devUsers['randa']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>80, 'new_progress'=>100, 'note'=>'ERD final disetujui, migration siap.', 'created_at'=>$today->copy()->subMonths(3)->subDays(13)],
            ['task_key'=>'p5_3', 'user_id'=>$devUsers['lutfhi']->id, 'old_status'=>'todo', 'new_status'=>'in_progress', 'old_progress'=>0, 'new_progress'=>50, 'note'=>'Login dan register selesai.', 'created_at'=>$today->copy()->subMonths(3)->subDays(12)],
            ['task_key'=>'p5_3', 'user_id'=>$devUsers['lutfhi']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>90, 'new_progress'=>100, 'note'=>'Manajemen user & role selesai.', 'created_at'=>$today->copy()->subMonths(3)->subDays(8)],
            ['task_key'=>'p5_4', 'user_id'=>$devUsers['bagus']->id, 'old_status'=>'todo', 'new_status'=>'in_progress', 'old_progress'=>0, 'new_progress'=>30, 'note'=>'Generate QR berhasil.', 'created_at'=>$today->copy()->subMonths(2)->subDays(28)],
            ['task_key'=>'p5_4', 'user_id'=>$devUsers['bagus']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>85, 'new_progress'=>100, 'note'=>'QR scan dan validasi selesai, diuji 50 karyawan.', 'created_at'=>$today->copy()->subMonths(2)->subDays(22)],
            ['task_key'=>'p5_5', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'todo', 'new_status'=>'in_progress', 'old_progress'=>0, 'new_progress'=>60, 'note'=>'Integrasi GPS browser berhasil.', 'created_at'=>$today->copy()->subMonths(2)->subDays(20)],
            ['task_key'=>'p5_5', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>90, 'new_progress'=>100, 'note'=>'Validasi radius GPS selesai dan diuji lapangan.', 'created_at'=>$today->copy()->subMonths(2)->subDays(16)],
            ['task_key'=>'p5_6', 'user_id'=>$devUsers['randa']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>80, 'new_progress'=>100, 'note'=>'Laporan rekapitulasi selesai, PDF berfungsi baik.', 'created_at'=>$today->copy()->subMonths(2)->subDays(8)],
            ['task_key'=>'p5_7', 'user_id'=>$devUsers['lutfhi']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>75, 'new_progress'=>100, 'note'=>'Dashboard monitoring selesai dengan chart real-time.', 'created_at'=>$today->copy()->subMonths(2)->subDays(3)],
            ['task_key'=>'p5_8', 'user_id'=>$devUsers['bagus']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>90, 'new_progress'=>100, 'note'=>'Semua bug critical sudah diperbaiki. Go live siap.', 'created_at'=>$today->copy()->subMonths(1)->subDays(3)],
            ['task_key'=>'p5_9', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>80, 'new_progress'=>100, 'note'=>'Deploy ke VPS selesai, domain aktif.', 'created_at'=>$today->copy()->subMonths(1)],

            // Proyek 6: E-Learning
            ['task_key'=>'p6_1', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>80, 'new_progress'=>100, 'note'=>'Rencana modul selesai: 5 kursus utama diidentifikasi.', 'created_at'=>$today->copy()->subMonths(1)->subDays(18)],
            ['task_key'=>'p6_2', 'user_id'=>$devUsers['randa']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>85, 'new_progress'=>100, 'note'=>'Skema DB disetujui, relasi many-to-many user-course siap.', 'created_at'=>$today->copy()->subMonths(1)->subDays(12)],
            ['task_key'=>'p6_3', 'user_id'=>$devUsers['lutfhi']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>90, 'new_progress'=>100, 'note'=>'Halaman listing dan detail kursus selesai.', 'created_at'=>$today->copy()->subDays(16)],
            ['task_key'=>'p6_4', 'user_id'=>$devUsers['bagus']->id, 'old_status'=>'todo', 'new_status'=>'in_progress', 'old_progress'=>0, 'new_progress'=>30, 'note'=>'Mulai kerjakan sistem kuis pilihan ganda.', 'created_at'=>$today->copy()->subDays(8)],
            ['task_key'=>'p6_4', 'user_id'=>$devUsers['bagus']->id, 'old_status'=>'in_progress', 'new_status'=>'in_progress', 'old_progress'=>30, 'new_progress'=>60, 'note'=>'Kuis pilihan ganda selesai, sedang kerjakan kalkulasi nilai.', 'created_at'=>$today->copy()->subDays(2)],
            ['task_key'=>'p6_5', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'todo', 'new_status'=>'in_progress', 'old_progress'=>0, 'new_progress'=>45, 'note'=>'Upload video via S3 berhasil, streaming masih dikerjakan.', 'created_at'=>$today->copy()->subDays(5)],

            // Proyek tambahan historis
            ['task_key'=>'p1_h1', 'user_id'=>$devUsers['rizhan']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>80, 'new_progress'=>100, 'note'=>'Environment siap, semua tim bisa mulai develop.', 'created_at'=>$today->copy()->subMonths(3)->addDays(1)],
            ['task_key'=>'p1_h2', 'user_id'=>$devUsers['randa']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>75, 'new_progress'=>100, 'note'=>'Modul kategori selesai, sudah diuji admin.', 'created_at'=>$today->copy()->subMonths(2)->subDays(18)],
            ['task_key'=>'p1_h3', 'user_id'=>$devUsers['lutfhi']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>85, 'new_progress'=>100, 'note'=>'Notifikasi email stok minimum berfungsi.', 'created_at'=>$today->copy()->subMonths(2)->subDays(7)],
            ['task_key'=>'p3_h1', 'user_id'=>$devUsers['lutfhi']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>90, 'new_progress'=>100, 'note'=>'Midtrans berhasil terintegrasi, transaksi test berhasil.', 'created_at'=>$today->copy()->subMonths(2)->subDays(2)],
            ['task_key'=>'p4_h1', 'user_id'=>$devUsers['randa']->id, 'old_status'=>'in_progress', 'new_status'=>'done', 'old_progress'=>80, 'new_progress'=>100, 'note'=>'Push notification berhasil terkirim ke perangkat test.', 'created_at'=>$today->copy()->subMonths(1)->subDays(3)],
        ];

        foreach ($logs as $logData) {
            $taskKey = $logData['task_key'];
            if (!isset($createdTasks[$taskKey])) continue;

            $task = $createdTasks[$taskKey];

            TaskActivityLog::firstOrCreate(
                [
                    'task_id'      => $task->id,
                    'user_id'      => $logData['user_id'],
                    'old_status'   => $logData['old_status'],
                    'new_status'   => $logData['new_status'],
                    'old_progress' => $logData['old_progress'],
                    'new_progress' => $logData['new_progress'],
                ],
                [
                    'note'       => $logData['note'],
                    'created_at' => $logData['created_at'] ?? now(),
                    'updated_at' => $logData['created_at'] ?? now(),
                ]
            );
        }
    }

    // ────────────────────────────────────────────────────────────────────────────
    // HELPER: Seed comments
    // ────────────────────────────────────────────────────────────────────────────

    private function seedComments(array $createdTasks, array $devs, array $devUsers, $adminUser): void
    {
        $comments = [
            ['task_key'=>'p5_4', 'user_id'=>$adminUser->id, 'comment'=>'Bagus, pastikan QR yang di-generate unik per sesi (pakai UUID + timestamp) agar tidak bisa digunakan ulang.'],
            ['task_key'=>'p5_4', 'user_id'=>$devUsers['bagus']->id, 'comment'=>'Sudah pakai UUID v4 + timestamp encode base64. Setiap QR expire dalam 5 menit.'],
            ['task_key'=>'p5_5', 'user_id'=>$adminUser->id, 'comment'=>'Rizhan, radius toleransi GPS-nya berapa meter? Pastikan tidak terlalu ketat agar yang pakai HP lama tidak gagal absen.'],
            ['task_key'=>'p5_5', 'user_id'=>$devUsers['rizhan']->id, 'comment'=>'Kita set 100 meter dari titik kantor. Admin bisa ubah radius dari dashboard. Sudah ditest, akurasi 95%.'],
            ['task_key'=>'p5_8', 'user_id'=>$adminUser->id, 'comment'=>'Testing menyeluruh ya Bagus. Pastikan edge case: HP mati saat scan, GPS dimatikan, dan jaringan lambat.'],
            ['task_key'=>'p5_8', 'user_id'=>$devUsers['bagus']->id, 'comment'=>'Semua edge case sudah ditest. Ada fallback manual absensi jika GPS gagal, harus diapprove admin.'],

            ['task_key'=>'p6_4', 'user_id'=>$adminUser->id, 'comment'=>'Bagus, sistem kuis harus bisa handle beberapa tipe: pilihan ganda, benar/salah, dan isian singkat.'],
            ['task_key'=>'p6_4', 'user_id'=>$devUsers['bagus']->id, 'comment'=>'Pilihan ganda dan benar/salah sudah selesai. Isian singkat masih dikerjakan, rencananya pakai exact match + fuzzy match.'],
            ['task_key'=>'p6_5', 'user_id'=>$devUsers['rizhan']->id, 'comment'=>'Untuk video streaming pakai HLS (HTTP Live Streaming) agar bisa adaptive bitrate. Sudah test dengan video 720p.'],

            ['task_key'=>'p1_h3', 'user_id'=>$adminUser->id, 'comment'=>'Lutfhi, notifikasi stok minimum sebaiknya dikirim via email DAN in-app notification.'],
            ['task_key'=>'p1_h3', 'user_id'=>$devUsers['lutfhi']->id, 'comment'=>'Sudah implement keduanya. Email pakai queue agar tidak blocking. In-app via broadcast event.'],
            ['task_key'=>'p3_h1', 'user_id'=>$adminUser->id, 'comment'=>'Integrasi Midtrans harus support payment methods: transfer bank, QRIS, dan kartu kredit.'],
            ['task_key'=>'p3_h1', 'user_id'=>$devUsers['lutfhi']->id, 'comment'=>'Semua payment method sudah aktif. Sandbox test: 10 transaksi berhasil, 2 sengaja gagal untuk test error handling.'],
            ['task_key'=>'p3_h3', 'user_id'=>$adminUser->id, 'comment'=>'Bagus, tracking status harus real-time. Coba pakai polling setiap 30 detik atau websocket.'],
            ['task_key'=>'p4_h2', 'user_id'=>$adminUser->id, 'comment'=>'Lutfhi, enkripsi harus menggunakan key yang unik per user agar jika satu key bocor, tidak semua pesan terbaca.'],
            ['task_key'=>'p4_h2', 'user_id'=>$devUsers['lutfhi']->id, 'comment'=>'Implementasi key derivation per user menggunakan PBKDF2. Setiap pesan punya IV unik. Sudah audit security-nya.'],
        ];

        foreach ($comments as $commentData) {
            $taskKey = $commentData['task_key'];
            if (!isset($createdTasks[$taskKey])) continue;

            $task = $createdTasks[$taskKey];

            \App\Models\TaskComment::firstOrCreate([
                'task_id' => $task->id,
                'user_id' => $commentData['user_id'],
                'comment' => $commentData['comment'],
            ]);
        }
    }

    // ────────────────────────────────────────────────────────────────────────────
    // HELPER: Seed KPI Records (3 bulan terakhir)
    // ────────────────────────────────────────────────────────────────────────────

    private function seedKpiRecords(array $devs, Carbon $today): void
    {
        // KPI berdasarkan data tasks yang sudah ada
        // Data ini dibuat manual mengacu pada pattern task yang di-seed

        $kpiData = [
            // ── Rizhan: Fullstack, produktif dan efisien ──
            'rizhan' => [
                ['period' => $today->copy()->subMonths(3)->format('Y-m'), 'assigned'=>8, 'done'=>7, 'ontime'=>6, 'overdue_done'=>1, 'est_h'=>51, 'act_h'=>44, 'notes'=>'Kinerja sangat baik di bulan perdana.'],
                ['period' => $today->copy()->subMonths(2)->format('Y-m'), 'assigned'=>7, 'done'=>7, 'ontime'=>7, 'overdue_done'=>0, 'est_h'=>44, 'act_h'=>40, 'notes'=>'100% on-time, performa konsisten.'],
                ['period' => $today->copy()->subMonths(1)->format('Y-m'), 'assigned'=>6, 'done'=>5, 'ontime'=>5, 'overdue_done'=>0, 'est_h'=>34, 'act_h'=>31, 'notes'=>'Sedikit task karena onboarding proyek baru.'],
            ],
            // ── Randa: Backend, stabil dan teliti ──
            'randa' => [
                ['period' => $today->copy()->subMonths(3)->format('Y-m'), 'assigned'=>7, 'done'=>6, 'ontime'=>5, 'overdue_done'=>1, 'est_h'=>46, 'act_h'=>45, 'notes'=>'1 task sedikit terlambat karena scope bertambah.'],
                ['period' => $today->copy()->subMonths(2)->format('Y-m'), 'assigned'=>6, 'done'=>6, 'ontime'=>6, 'overdue_done'=>0, 'est_h'=>40, 'act_h'=>38, 'notes'=>'Performa meningkat, semua task on-time.'],
                ['period' => $today->copy()->subMonths(1)->format('Y-m'), 'assigned'=>5, 'done'=>4, 'ontime'=>4, 'overdue_done'=>0, 'est_h'=>32, 'act_h'=>30, 'notes'=>'Efisiensi baik, database design sangat solid.'],
            ],
            // ── Lutfhi: Frontend, kreatif tapi kadang over-estimate ──
            'lutfhi' => [
                ['period' => $today->copy()->subMonths(3)->format('Y-m'), 'assigned'=>7, 'done'=>6, 'ontime'=>4, 'overdue_done'=>2, 'est_h'=>50, 'act_h'=>55, 'notes'=>'2 task terlambat karena revisi desain dari client.'],
                ['period' => $today->copy()->subMonths(2)->format('Y-m'), 'assigned'=>6, 'done'=>5, 'ontime'=>5, 'overdue_done'=>0, 'est_h'=>40, 'act_h'=>42, 'notes'=>'Membaik, animasi UI mendapat pujian dari client.'],
                ['period' => $today->copy()->subMonths(1)->format('Y-m'), 'assigned'=>5, 'done'=>5, 'ontime'=>5, 'overdue_done'=>0, 'est_h'=>38, 'act_h'=>35, 'notes'=>'Excellent month, 100% on-time dengan kualitas tinggi.'],
            ],
            // ── Bagus: Backend, cepat tapi perlu perbaiki akurasi estimasi ──
            'bagus' => [
                ['period' => $today->copy()->subMonths(3)->format('Y-m'), 'assigned'=>6, 'done'=>5, 'ontime'=>3, 'overdue_done'=>2, 'est_h'=>44, 'act_h'=>51, 'notes'=>'Beberapa task membutuhkan waktu lebih dari estimasi.'],
                ['period' => $today->copy()->subMonths(2)->format('Y-m'), 'assigned'=>7, 'done'=>6, 'ontime'=>4, 'overdue_done'=>2, 'est_h'=>48, 'act_h'=>54, 'notes'=>'Masih ada gap estimasi, perlu lebih cermat.'],
                ['period' => $today->copy()->subMonths(1)->format('Y-m'), 'assigned'=>5, 'done'=>5, 'ontime'=>5, 'overdue_done'=>0, 'est_h'=>40, 'act_h'=>42, 'notes'=>'Peningkatan signifikan, semua task selesai on-time.'],
            ],
        ];

        foreach ($kpiData as $devKey => $months) {
            $developer = $devs[$devKey];
            foreach ($months as $m) {
                $scores = DeveloperKpi::calculateScore(
                    $m['done'],
                    $m['ontime'],
                    max(1, $m['assigned']),
                    (float) $m['est_h'],
                    (float) $m['act_h']
                );

                DeveloperKpi::updateOrCreate(
                    ['developer_id' => $developer->id, 'period_month' => $m['period']],
                    array_merge($scores, [
                        'tasks_assigned'        => $m['assigned'],
                        'tasks_done'            => $m['done'],
                        'tasks_ontime'          => $m['ontime'],
                        'tasks_overdue_done'    => $m['overdue_done'],
                        'total_estimated_hours' => $m['est_h'],
                        'total_actual_hours'    => $m['act_h'],
                        'notes'                 => $m['notes'],
                    ])
                );
            }
        }
    }
}
