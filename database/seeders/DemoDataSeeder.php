<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Developer;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskActivityLog;
use App\Models\TaskComment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DemoDataSeeder — Seeder idempotent untuk demo skripsi Codevision.
 *
 * Cara menjalankan:
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * Seeder ini AMAN untuk dijalankan berulang (tidak membuat duplikat berlebihan).
 * Menggunakan firstOrCreate / updateOrCreate di semua entitas utama.
 * Tidak menghapus data lama, tidak migrate:fresh.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // ================================================================
        // 1. USERS
        // Kolom yang ada: id, name, email, role, email_verified_at,
        //                 password, remember_token, created_at, updated_at
        // ================================================================

        // Admin (buat hanya jika belum ada sama sekali)
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@codevision.test'],
            [
                'name'              => 'Admin Codevision',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Developer Users
        $userRizhan = User::firstOrCreate(
            ['email' => 'rizhan@codevision.test'],
            [
                'name'              => 'Rizhan',
                'password'          => Hash::make('password'),
                'role'              => 'developer',
                'email_verified_at' => now(),
            ]
        );

        $userRanda = User::firstOrCreate(
            ['email' => 'randa@codevision.test'],
            [
                'name'              => 'Randa',
                'password'          => Hash::make('password'),
                'role'              => 'developer',
                'email_verified_at' => now(),
            ]
        );

        $userLutfhi = User::firstOrCreate(
            ['email' => 'lutfhi@codevision.test'],
            [
                'name'              => 'Lutfhi Arifin',
                'password'          => Hash::make('password'),
                'role'              => 'developer',
                'email_verified_at' => now(),
            ]
        );

        $userBagus = User::firstOrCreate(
            ['email' => 'bagus@codevision.test'],
            [
                'name'              => 'Bagus',
                'password'          => Hash::make('password'),
                'role'              => 'developer',
                'email_verified_at' => now(),
            ]
        );

        // Client Users
        $userKarya = User::firstOrCreate(
            ['email' => 'client.karya@demo.test'],
            [
                'name'              => 'CV Karya Mandiri',
                'password'          => Hash::make('password'),
                'role'              => 'client',
                'email_verified_at' => now(),
            ]
        );

        $userUmkm = User::firstOrCreate(
            ['email' => 'client.umkm@demo.test'],
            [
                'name'              => 'Rumah UMKM Banjarmasin',
                'password'          => Hash::make('password'),
                'role'              => 'client',
                'email_verified_at' => now(),
            ]
        );

        $userBerkah = User::firstOrCreate(
            ['email' => 'client.berkah@demo.test'],
            [
                'name'              => 'Toko Berkah Digital',
                'password'          => Hash::make('password'),
                'role'              => 'client',
                'email_verified_at' => now(),
            ]
        );

        // Client User — Say It Last
        $userSayItLast = User::firstOrCreate(
            ['email' => 'client.sayitlast@demo.test'],
            [
                'name'              => 'say it last.com',
                'password'          => Hash::make('password'),
                'role'              => 'client',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✔ Users selesai dibuat/ditemukan.');

        // ================================================================
        // 2. DEVELOPER RECORDS (terhubung ke User)
        // ================================================================

        $devRizhan = Developer::firstOrCreate(
            ['email' => 'rizhan@codevision.test'],
            [
                'user_id' => $userRizhan->id,
                'name'    => 'Rizhan',
                'role'    => 'fullstack',
                'skill'   => 'Laravel, Vue.js, MySQL',
            ]
        );
        // Pastikan user_id tersinkron jika record sudah ada
        if ($devRizhan->user_id !== $userRizhan->id) {
            $devRizhan->update(['user_id' => $userRizhan->id]);
        }

        $devRanda = Developer::firstOrCreate(
            ['email' => 'randa@codevision.test'],
            [
                'user_id' => $userRanda->id,
                'name'    => 'Randa',
                'role'    => 'backend',
                'skill'   => 'PHP, Laravel, PostgreSQL',
            ]
        );
        if ($devRanda->user_id !== $userRanda->id) {
            $devRanda->update(['user_id' => $userRanda->id]);
        }

        $devLutfhi = Developer::firstOrCreate(
            ['email' => 'lutfhi@codevision.test'],
            [
                'user_id' => $userLutfhi->id,
                'name'    => 'Lutfhi Arifin',
                'role'    => 'frontend',
                'skill'   => 'React, Tailwind CSS, JavaScript',
            ]
        );
        if ($devLutfhi->user_id !== $userLutfhi->id) {
            $devLutfhi->update(['user_id' => $userLutfhi->id]);
        }

        $devBagus = Developer::firstOrCreate(
            ['email' => 'bagus@codevision.test'],
            [
                'user_id' => $userBagus->id,
                'name'    => 'Bagus',
                'role'    => 'backend',
                'skill'   => 'Node.js, Express, MongoDB',
            ]
        );
        if ($devBagus->user_id !== $userBagus->id) {
            $devBagus->update(['user_id' => $userBagus->id]);
        }

        $this->command->info('✔ Developer records selesai dibuat/ditemukan.');

        // ================================================================
        // 3. CLIENT RECORDS (terhubung ke User)
        // ================================================================

        $clientKarya = Client::firstOrCreate(
            ['email' => 'client.karya@demo.test'],
            [
                'user_id' => $userKarya->id,
                'name'    => 'CV Karya Mandiri',
                'phone'   => '0812-3456-7890',
                'company' => 'CV Karya Mandiri',
            ]
        );
        if ($clientKarya->user_id !== $userKarya->id) {
            $clientKarya->update(['user_id' => $userKarya->id]);
        }

        $clientUmkm = Client::firstOrCreate(
            ['email' => 'client.umkm@demo.test'],
            [
                'user_id' => $userUmkm->id,
                'name'    => 'Rumah UMKM Banjarmasin',
                'phone'   => '0811-2233-4455',
                'company' => 'Rumah UMKM Banjarmasin',
            ]
        );
        if ($clientUmkm->user_id !== $userUmkm->id) {
            $clientUmkm->update(['user_id' => $userUmkm->id]);
        }

        $clientBerkah = Client::firstOrCreate(
            ['email' => 'client.berkah@demo.test'],
            [
                'user_id' => $userBerkah->id,
                'name'    => 'Toko Berkah Digital',
                'phone'   => '0822-9988-7766',
                'company' => 'Toko Berkah Digital',
            ]
        );
        if ($clientBerkah->user_id !== $userBerkah->id) {
            $clientBerkah->update(['user_id' => $userBerkah->id]);
        }

        $clientSayItLast = Client::firstOrCreate(
            ['email' => 'client.sayitlast@demo.test'],
            [
                'user_id' => $userSayItLast->id,
                'name'    => 'say it last.com',
                'phone'   => '0813-1234-5678',
                'company' => 'say it last.com',
            ]
        );
        if ($clientSayItLast->user_id !== $userSayItLast->id) {
            $clientSayItLast->update(['user_id' => $userSayItLast->id]);
        }

        $this->command->info('✔ Client records selesai dibuat/ditemukan.');

        // ================================================================
        // 4. PROJECT
        // Enum status DB: planned | on_progress | completed
        // "Execution" → on_progress | "Planning" → planned
        // ================================================================

        $proj1 = Project::updateOrCreate(
            ['name' => 'Sistem Informasi Inventaris Barang'],
            [
                'client_id'   => $clientKarya->id,
                'client_name' => 'CV Karya Mandiri',
                'status'      => 'on_progress',
                'start_date'  => $today->copy()->subMonths(2)->format('Y-m-d'),
                'end_date'    => $today->copy()->addMonths(2)->format('Y-m-d'),
                'description' => 'Aplikasi web untuk mengelola data barang, stok masuk, stok keluar, dan laporan inventaris.',
            ]
        );

        $proj2 = Project::updateOrCreate(
            ['name' => 'Website Company Profile UMKM'],
            [
                'client_id'   => $clientUmkm->id,
                'client_name' => 'Rumah UMKM Banjarmasin',
                'status'      => 'on_progress',
                'start_date'  => $today->copy()->subMonth()->format('Y-m-d'),
                'end_date'    => $today->copy()->addMonths(3)->format('Y-m-d'),
                'description' => 'Website profil untuk menampilkan informasi usaha, galeri produk, layanan, dan kontak.',
            ]
        );

        $proj3 = Project::updateOrCreate(
            ['name' => 'Aplikasi Manajemen Pemesanan Online'],
            [
                'client_id'   => $clientBerkah->id,
                'client_name' => 'Toko Berkah Digital',
                'status'      => 'on_progress',
                'start_date'  => $today->copy()->subMonths(3)->format('Y-m-d'),
                'end_date'    => $today->copy()->addMonths(1)->format('Y-m-d'),
                'description' => 'Aplikasi untuk mencatat pesanan pelanggan, status pembayaran, dan laporan transaksi.',
            ]
        );

        $proj4 = Project::updateOrCreate(
            ['name' => 'Say It Last'],
            [
                'client_id'   => $clientSayItLast->id,
                'client_name' => 'say it last.com',
                'status'      => 'on_progress',
                'start_date'  => $today->copy()->subWeeks(3)->format('Y-m-d'),
                'end_date'    => $today->copy()->addMonths(4)->format('Y-m-d'),
                'description' => 'Say It Last adalah aplikasi berbasis web yang digunakan untuk membuat, menyimpan, dan mengelola pesan terakhir atau pesan pribadi yang dapat dikirimkan kepada penerima tertentu sesuai kondisi atau waktu yang ditentukan. Sistem ini mencakup pengelolaan akun pengguna, pesan, penerima, jadwal pengiriman, dan status pesan.',
            ]
        );

        $this->command->info('✔ Proyek selesai dibuat/diperbarui.');

        // ================================================================
        // 5. TASKS
        // Deadline bervariasi:
        //   - H-3  → today + 3 hari
        //   - H-1  → today + 1 hari
        //   - Overdue → today - n hari, status bukan done
        //   - Done  → deadline sudah lewat tapi status done (tidak overdue)
        // ================================================================

        // --- Proyek 1: Sistem Informasi Inventaris Barang ---

        $task1_1 = Task::updateOrCreate(
            [
                'project_id' => $proj1->id,
                'title'      => 'Analisis kebutuhan modul inventaris',
            ],
            [
                'developer_id'    => $devRizhan->id,
                'status'          => 'done',
                'progress'        => 100,
                'deadline'        => $today->copy()->subDays(10)->format('Y-m-d'), // done tepat waktu
                'estimated_hours' => 6,
                'actual_hours'    => 5,
                'description'     => 'Melakukan analisis kebutuhan fungsional dan non-fungsional untuk modul inventaris barang.',
            ]
        );

        $task1_2 = Task::updateOrCreate(
            [
                'project_id' => $proj1->id,
                'title'      => 'Membuat desain database barang dan transaksi',
            ],
            [
                'developer_id'    => $devRanda->id,
                'status'          => 'done',
                'progress'        => 100,
                'deadline'        => $today->copy()->subDays(5)->format('Y-m-d'),  // done tepat waktu
                'estimated_hours' => 8,
                'actual_hours'    => 9,
                'description'     => 'Merancang ERD dan skema database untuk entitas barang, kategori, dan transaksi stok.',
            ]
        );

        $task1_3 = Task::updateOrCreate(
            [
                'project_id' => $proj1->id,
                'title'      => 'Implementasi CRUD data barang',
            ],
            [
                'developer_id'    => $devLutfhi->id,
                'status'          => 'in_progress',
                'progress'        => 65,
                'deadline'        => $today->copy()->addDays(3)->format('Y-m-d'),  // H-3
                'estimated_hours' => 12,
                'actual_hours'    => 7,
                'description'     => 'Membuat fitur tambah, ubah, hapus, dan tampilkan data barang beserta validasi form.',
            ]
        );

        $task1_4 = Task::updateOrCreate(
            [
                'project_id' => $proj1->id,
                'title'      => 'Membuat laporan stok masuk dan keluar',
            ],
            [
                'developer_id'    => $devBagus->id,
                'status'          => 'todo',
                'progress'        => 10,
                'deadline'        => $today->copy()->subDays(2)->format('Y-m-d'),  // OVERDUE
                'estimated_hours' => 10,
                'actual_hours'    => 0,
                'description'     => 'Membuat laporan rekap transaksi stok masuk dan keluar dalam format tabel dan PDF.',
            ]
        );

        // --- Proyek 2: Website Company Profile UMKM ---

        $task2_1 = Task::updateOrCreate(
            [
                'project_id' => $proj2->id,
                'title'      => 'Membuat halaman beranda',
            ],
            [
                'developer_id'    => $devRizhan->id,
                'status'          => 'done',
                'progress'        => 100,
                'deadline'        => $today->copy()->subDays(7)->format('Y-m-d'),  // done tepat waktu
                'estimated_hours' => 5,
                'actual_hours'    => 4,
                'description'     => 'Membuat halaman beranda dengan hero section, informasi singkat, dan call-to-action.',
            ]
        );

        $task2_2 = Task::updateOrCreate(
            [
                'project_id' => $proj2->id,
                'title'      => 'Membuat halaman profil dan layanan',
            ],
            [
                'developer_id'    => $devBagus->id,
                'status'          => 'in_progress',
                'progress'        => 70,
                'deadline'        => $today->copy()->addDay()->format('Y-m-d'),    // H-1
                'estimated_hours' => 6,
                'actual_hours'    => 4,
                'description'     => 'Membuat halaman tentang kami, layanan yang ditawarkan, dan informasi tim.',
            ]
        );

        $task2_3 = Task::updateOrCreate(
            [
                'project_id' => $proj2->id,
                'title'      => 'Membuat halaman galeri produk',
            ],
            [
                'developer_id'    => $devRanda->id,
                'status'          => 'todo',
                'progress'        => 20,
                'deadline'        => $today->copy()->subDays(3)->format('Y-m-d'),  // OVERDUE
                'estimated_hours' => 7,
                'actual_hours'    => 0,
                'description'     => 'Membuat galeri foto produk dengan fitur lightbox dan filter kategori.',
            ]
        );

        $task2_4 = Task::updateOrCreate(
            [
                'project_id' => $proj2->id,
                'title'      => 'Integrasi form kontak',
            ],
            [
                'developer_id'    => $devLutfhi->id,
                'status'          => 'todo',
                'progress'        => 0,
                'deadline'        => $today->copy()->addDays(3)->format('Y-m-d'),  // H-3
                'estimated_hours' => 5,
                'actual_hours'    => 0,
                'description'     => 'Integrasi form kontak dengan validasi, notifikasi email, dan penyimpanan pesan ke database.',
            ]
        );

        // --- Proyek 3: Aplikasi Manajemen Pemesanan Online ---

        $task3_1 = Task::updateOrCreate(
            [
                'project_id' => $proj3->id,
                'title'      => 'Membuat modul login dan hak akses',
            ],
            [
                'developer_id'    => $devLutfhi->id,
                'status'          => 'done',
                'progress'        => 100,
                'deadline'        => $today->copy()->subDays(14)->format('Y-m-d'), // done tepat waktu
                'estimated_hours' => 8,
                'actual_hours'    => 7,
                'description'     => 'Membuat sistem autentikasi login, registrasi, dan manajemen role pengguna (admin, kasir).',
            ]
        );

        $task3_2 = Task::updateOrCreate(
            [
                'project_id' => $proj3->id,
                'title'      => 'Membuat modul data produk',
            ],
            [
                'developer_id'    => $devRanda->id,
                'status'          => 'in_progress',
                'progress'        => 50,
                'deadline'        => $today->copy()->addDay()->format('Y-m-d'),    // H-1
                'estimated_hours' => 10,
                'actual_hours'    => 5,
                'description'     => 'Membuat manajemen data produk termasuk kategori, harga, stok, dan foto produk.',
            ]
        );

        $task3_3 = Task::updateOrCreate(
            [
                'project_id' => $proj3->id,
                'title'      => 'Membuat modul pesanan pelanggan',
            ],
            [
                'developer_id'    => $devRizhan->id,
                'status'          => 'in_progress',
                'progress'        => 45,
                'deadline'        => $today->copy()->subDays(1)->format('Y-m-d'),  // OVERDUE (kemarin)
                'estimated_hours' => 12,
                'actual_hours'    => 6,
                'description'     => 'Membuat modul pencatatan pesanan pelanggan, update status pesanan, dan notifikasi.',
            ]
        );

        $task3_4 = Task::updateOrCreate(
            [
                'project_id' => $proj3->id,
                'title'      => 'Membuat laporan transaksi',
            ],
            [
                'developer_id'    => $devBagus->id,
                'status'          => 'todo',
                'progress'        => 0,
                'deadline'        => $today->copy()->addDays(3)->format('Y-m-d'),  // H-3
                'estimated_hours' => 9,
                'actual_hours'    => 0,
                'description'     => 'Membuat laporan transaksi harian, bulanan, dan rekap pendapatan dalam format PDF.',
            ]
        );

        // --- Proyek 4: Say It Last ---

        $task4_1 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Analisis kebutuhan fitur Say It Last',
            ],
            [
                'developer_id'    => $devRizhan->id,
                'status'          => 'done',
                'progress'        => 100,
                'deadline'        => $today->copy()->subDays(15)->format('Y-m-d'), // done tepat waktu
                'estimated_hours' => 6,
                'actual_hours'    => 5,
                'description'     => 'Melakukan analisis kebutuhan fungsional dan non-fungsional untuk semua fitur Say It Last: pesan, penerima, jadwal, dan status.',
            ]
        );

        $task4_2 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Membuat desain database pengguna, pesan, dan penerima',
            ],
            [
                'developer_id'    => $devRanda->id,
                'status'          => 'done',
                'progress'        => 100,
                'deadline'        => $today->copy()->subDays(10)->format('Y-m-d'), // done tepat waktu
                'estimated_hours' => 8,
                'actual_hours'    => 8,
                'description'     => 'Merancang ERD dan skema database untuk entitas users, messages, recipients, schedules, dan message_status.',
            ]
        );

        $task4_3 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Implementasi autentikasi dan manajemen akun pengguna',
            ],
            [
                'developer_id'    => $devLutfhi->id,
                'status'          => 'in_progress',
                'progress'        => 70,
                'deadline'        => $today->copy()->addDays(4)->format('Y-m-d'), // H-4
                'estimated_hours' => 10,
                'actual_hours'    => 6,
                'description'     => 'Membuat fitur registrasi, login, logout, verifikasi email, dan manajemen profil pengguna.',
            ]
        );

        $task4_4 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Membuat fitur tambah dan edit pesan terakhir',
            ],
            [
                'developer_id'    => $devBagus->id,
                'status'          => 'in_progress',
                'progress'        => 55,
                'deadline'        => $today->copy()->addDays(6)->format('Y-m-d'), // H-6
                'estimated_hours' => 9,
                'actual_hours'    => 5,
                'description'     => 'Membuat form pembuatan pesan terakhir dengan rich text editor, lampiran file, dan pengaturan penerima.',
            ]
        );

        $task4_5 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Membuat fitur jadwal pengiriman pesan',
            ],
            [
                'developer_id'    => $devRanda->id,
                'status'          => 'todo',
                'progress'        => 0,
                'deadline'        => $today->copy()->addDays(14)->format('Y-m-d'),
                'estimated_hours' => 12,
                'actual_hours'    => 0,
                'description'     => 'Membuat sistem penjadwalan pengiriman pesan berbasis waktu atau kondisi tertentu yang ditentukan pengguna.',
            ]
        );

        $task4_6 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Membuat dashboard status pesan',
            ],
            [
                'developer_id'    => $devRizhan->id,
                'status'          => 'todo',
                'progress'        => 10,
                'deadline'        => $today->copy()->addDays(18)->format('Y-m-d'),
                'estimated_hours' => 8,
                'actual_hours'    => 0,
                'description'     => 'Membuat dashboard yang menampilkan status setiap pesan: draft, terjadwal, terkirim, dan gagal kirim.',
            ]
        );

        $task4_7 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Pengujian fitur pengiriman pesan',
            ],
            [
                'developer_id'    => $devLutfhi->id,
                'status'          => 'todo',
                'progress'        => 0,
                'deadline'        => $today->copy()->addDays(22)->format('Y-m-d'),
                'estimated_hours' => 7,
                'actual_hours'    => 0,
                'description'     => 'Pengujian end-to-end fitur pengiriman pesan: unit test, integration test, dan user acceptance test.',
            ]
        );

        // Task ke-8: OVERDUE — deadline kemarin, status in_progress
        $task4_8 = Task::updateOrCreate(
            [
                'project_id' => $proj4->id,
                'title'      => 'Perbaikan bug validasi penerima pesan',
            ],
            [
                'developer_id'    => $devBagus->id,
                'status'          => 'in_progress',
                'progress'        => 60,
                'deadline'        => $today->copy()->subDay()->format('Y-m-d'), // OVERDUE (kemarin)
                'estimated_hours' => 5,
                'actual_hours'    => 3,
                'description'     => 'Memperbaiki bug pada validasi input penerima pesan: format email tidak terdeteksi dan duplikasi penerima tidak dicegah.',
            ]
        );

        $this->command->info('✔ Task-task selesai dibuat/diperbarui.');

        // ================================================================
        // 6. TASK ACTIVITY LOGS
        // firstOrCreate: kombinasi task_id + user_id + old_status + new_status
        // ================================================================

        // Log 1: todo → in_progress pada task CRUD data barang
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task1_3->id,
                'user_id'    => $adminUser->id,
                'old_status' => 'todo',
                'new_status' => 'in_progress',
            ],
            [
                'old_progress' => 0,
                'new_progress' => 0,
                'note'         => 'Task mulai dikerjakan oleh Lutfhi Arifin. Proses analisis awal sudah selesai.',
            ]
        );

        // Log 2: Perubahan progres 20 → 50 pada task Modul Data Produk
        TaskActivityLog::firstOrCreate(
            [
                'task_id'      => $task3_2->id,
                'user_id'      => $userRanda->id,
                'old_progress' => 20,
                'new_progress' => 50,
            ],
            [
                'old_status' => 'in_progress',
                'new_status' => 'in_progress',
                'note'       => 'Update progres: fitur CRUD produk selesai, sedang mengerjakan validasi dan foto produk.',
            ]
        );

        // Log 3: in_progress → done pada task Membuat halaman beranda
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task2_1->id,
                'user_id'    => $userRizhan->id,
                'old_status' => 'in_progress',
                'new_status' => 'done',
            ],
            [
                'old_progress' => 80,
                'new_progress' => 100,
                'note'         => 'Halaman beranda selesai dikerjakan. Sudah direview dan disetujui oleh tim desain.',
            ]
        );

        // Log 4: todo → in_progress pada task Analisis kebutuhan (oleh admin)
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task1_1->id,
                'user_id'    => $adminUser->id,
                'old_status' => 'todo',
                'new_status' => 'in_progress',
            ],
            [
                'old_progress' => 0,
                'new_progress' => 30,
                'note'         => 'Rizhan memulai analisis kebutuhan, koordinasi dengan client CV Karya Mandiri.',
            ]
        );

        // Log 5: in_progress → done pada task Analisis kebutuhan
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task1_1->id,
                'user_id'    => $userRizhan->id,
                'old_status' => 'in_progress',
                'new_status' => 'done',
            ],
            [
                'old_progress' => 80,
                'new_progress' => 100,
                'note'         => 'Analisis kebutuhan selesai. Dokumen requirement sudah didokumentasikan dan disetujui.',
            ]
        );

        // Log 6: in_progress → done pada task Login & Hak Akses
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task3_1->id,
                'user_id'    => $userLutfhi->id,
                'old_status' => 'in_progress',
                'new_status' => 'done',
            ],
            [
                'old_progress' => 90,
                'new_progress' => 100,
                'note'         => 'Modul login dan hak akses selesai. Semua role sudah diuji: admin dan kasir.',
            ]
        );

        // ── Say It Last Activity Logs ──

        // Log: todo → done pada task Analisis kebutuhan Say It Last
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task4_1->id,
                'user_id'    => $adminUser->id,
                'old_status' => 'todo',
                'new_status' => 'in_progress',
            ],
            [
                'old_progress' => 0,
                'new_progress' => 50,
                'note'         => 'Rizhan mulai analisis kebutuhan Say It Last. Koordinasi awal dengan client say it last.com sudah dilakukan.',
            ]
        );

        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task4_1->id,
                'user_id'    => $userRizhan->id,
                'old_status' => 'in_progress',
                'new_status' => 'done',
            ],
            [
                'old_progress' => 80,
                'new_progress' => 100,
                'note'         => 'Analisis kebutuhan selesai. Dokumen SRS sudah disetujui oleh client.',
            ]
        );

        // Log: todo → done pada task Desain Database Say It Last
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task4_2->id,
                'user_id'    => $userRanda->id,
                'old_status' => 'in_progress',
                'new_status' => 'done',
            ],
            [
                'old_progress' => 85,
                'new_progress' => 100,
                'note'         => 'ERD dan skema database selesai dirancang. Sudah review bersama Rizhan dan disetujui.',
            ]
        );

        // Log: progress update pada task Implementasi Autentikasi
        TaskActivityLog::firstOrCreate(
            [
                'task_id'      => $task4_3->id,
                'user_id'      => $userLutfhi->id,
                'old_progress' => 30,
                'new_progress' => 70,
            ],
            [
                'old_status' => 'in_progress',
                'new_status' => 'in_progress',
                'note'       => 'Fitur registrasi dan login selesai. Sedang mengerjakan verifikasi email dan manajemen profil.',
            ]
        );

        // Log: bug report pada task Perbaikan Bug Validasi (overdue)
        TaskActivityLog::firstOrCreate(
            [
                'task_id'    => $task4_8->id,
                'user_id'    => $adminUser->id,
                'old_status' => 'todo',
                'new_status' => 'in_progress',
            ],
            [
                'old_progress' => 0,
                'new_progress' => 60,
                'note'         => 'Bug kritikal ditemukan: validasi format email penerima tidak berjalan. Bagus segera tangani, deadline sudah lewat.',
            ]
        );

        $this->command->info('✔ Task Activity Logs selesai dibuat.');

        // ================================================================
        // 7. TASK COMMENTS
        // firstOrCreate: kombinasi task_id + user_id + comment (truncated match)
        // ================================================================

        // Admin memberi arahan pada task CRUD data barang
        TaskComment::firstOrCreate(
            [
                'task_id' => $task1_3->id,
                'user_id' => $adminUser->id,
                'comment' => 'Pastikan validasi form menggunakan Laravel Request Validation ya, Lutfhi. Tambahkan pesan error yang informatif untuk user.',
            ]
        );

        // Developer mencatat kendala teknis pada task Modul Pesanan Pelanggan
        TaskComment::firstOrCreate(
            [
                'task_id' => $task3_3->id,
                'user_id' => $userRizhan->id,
                'comment' => 'Kendala: ada konflik pada relasi antara tabel orders dan order_items saat melakukan eager loading. Sedang investigasi solusinya.',
            ]
        );

        // Developer memberi update progres pada task Modul Data Produk
        TaskComment::firstOrCreate(
            [
                'task_id' => $task3_2->id,
                'user_id' => $userRanda->id,
                'comment' => 'Update: CRUD produk dan kategori sudah selesai (50%). Sekarang mengerjakan fitur upload foto multi-gambar.',
            ]
        );

        // Admin memberi arahan pada task Laporan Stok
        TaskComment::firstOrCreate(
            [
                'task_id' => $task1_4->id,
                'user_id' => $adminUser->id,
                'comment' => 'Bagus, untuk format laporan PDF gunakan library DomPDF yang sudah tersedia di project ini. Lihat contoh di ReportController.',
            ]
        );

        // Developer memberi update progres pada task Halaman Profil dan Layanan
        TaskComment::firstOrCreate(
            [
                'task_id' => $task2_2->id,
                'user_id' => $userBagus->id,
                'comment' => 'Progres 70%: Halaman profil dan tim sudah selesai. Sisa bagian halaman layanan dan animasi scroll.',
            ]
        );

        // Developer mencatat kendala pada task Galeri Produk
        TaskComment::firstOrCreate(
            [
                'task_id' => $task2_3->id,
                'user_id' => $userRanda->id,
                'comment' => 'Kendala: library lightbox yang dipakai tidak kompatibel dengan Bootstrap 5. Sedang mencari alternatif (Fancybox/GLightbox).',
            ]
        );

        // Admin merespon kendala
        TaskComment::firstOrCreate(
            [
                'task_id' => $task2_3->id,
                'user_id' => $adminUser->id,
                'comment' => 'Gunakan GLightbox saja, Randa. Sudah terbukti kompatibel dengan Bootstrap 5. Deadline perlu diperhatikan ya.',
            ]
        );

        // Developer konfirmasi modul login selesai
        TaskComment::firstOrCreate(
            [
                'task_id' => $task3_1->id,
                'user_id' => $userLutfhi->id,
                'comment' => 'Modul login selesai. Sudah diuji untuk role admin dan kasir. Screenshot testing ada di Google Drive tim.',
            ]
        );

        // ── Say It Last Task Comments ──

        // Admin memberi arahan awal pada task analisis Say It Last
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_1->id,
                'user_id' => $adminUser->id,
                'comment' => 'Rizhan, pastikan analisis kebutuhan mencakup semua skenario pengiriman pesan: terjadwal, berdasarkan kondisi, dan manual. Konsultasikan juga dengan client untuk edge case.',
            ]
        );

        // Developer lapor selesai analisis
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_1->id,
                'user_id' => $userRizhan->id,
                'comment' => 'Analisis selesai. Ada 3 skenario utama: (1) pesan terjadwal berdasarkan tanggal, (2) pesan dipicu kondisi manual oleh admin, (3) pesan berulang periodik. Semua sudah terdokumentasi di SRS.',
            ]
        );

        // Developer update ERD di task desain database
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_2->id,
                'user_id' => $userRanda->id,
                'comment' => 'ERD selesai. Ada 5 tabel utama: users, messages, recipients, schedules, message_logs. Relasi many-to-many antara messages dan recipients sudah dihandle dengan pivot table.',
            ]
        );

        // Admin feedback desain database
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_2->id,
                'user_id' => $adminUser->id,
                'comment' => 'Desain database sudah bagus Randa. Tambahkan kolom `sent_at` dan `failed_reason` di tabel message_logs untuk keperluan tracking pengiriman.',
            ]
        );

        // Developer update progres autentikasi
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_3->id,
                'user_id' => $userLutfhi->id,
                'comment' => 'Progress 70%: Login dan registrasi sudah selesai. Verifikasi email pakai Laravel built-in MustVerifyEmail. Sedang handle edge case akun tidak aktif.',
            ]
        );

        // Admin tanya progres task fitur pesan
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_4->id,
                'user_id' => $adminUser->id,
                'comment' => 'Bagus, bagaimana progres fitur tambah pesan? Pastikan rich text editor bisa handle format teks, gambar inline, dan emoji.',
            ]
        );

        // Developer jawab progres fitur pesan
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_4->id,
                'user_id' => $userBagus->id,
                'comment' => 'Progres 55%: Form pesan sudah jalan dengan Quill editor. Fitur lampiran file masih dalam pengerjaan. Target selesai 2 hari lagi.',
            ]
        );

        // Admin eskalasi bug overdue
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_8->id,
                'user_id' => $adminUser->id,
                'comment' => 'Bagus, bug ini sudah overdue! Prioritaskan segera. Bug duplikasi penerima bisa menyebabkan pesan terkirim dobel ke orang yang sama.',
            ]
        );

        // Developer respon bug
        TaskComment::firstOrCreate(
            [
                'task_id' => $task4_8->id,
                'user_id' => $userBagus->id,
                'comment' => 'Sudah ditemukan root cause-nya: validasi unique pada email penerima tidak dijalankan saat update. Fix sedang dikerjakan, estimasi selesai hari ini.',
            ]
        );

        $this->command->info('✔ Task Comments selesai dibuat.');

        // ================================================================
        // RINGKASAN AKHIR
        // ================================================================
        $this->command->newLine();
        $this->command->info('================================================================');
        $this->command->info('  DemoDataSeeder selesai! Data demo berhasil dibuat/diperbarui.');
        $this->command->info('================================================================');
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',     'admin@codevision.test',         'password'],
                ['Developer', 'rizhan@codevision.test',        'password'],
                ['Developer', 'randa@codevision.test',         'password'],
                ['Developer', 'lutfhi@codevision.test',        'password'],
                ['Developer', 'bagus@codevision.test',         'password'],
                ['Client',    'client.karya@demo.test',        'password'],
                ['Client',    'client.umkm@demo.test',         'password'],
                ['Client',    'client.berkah@demo.test',       'password'],
                ['Client',    'client.sayitlast@demo.test',    'password'],
            ]
        );
        $this->command->newLine();
        $this->command->info('Proyek demo yang tersedia:');
        $this->command->info('  1. Sistem Informasi Inventaris Barang  (CV Karya Mandiri)');
        $this->command->info('  2. Website Company Profile UMKM        (Rumah UMKM Banjarmasin)');
        $this->command->info('  3. Aplikasi Manajemen Pemesanan Online  (Toko Berkah Digital)');
        $this->command->info('  4. Say It Last                          (say it last.com)  ← BARU');
        $this->command->newLine();
        $this->command->info('Distribusi deadline tasks:');
        $this->command->info('  H-3  (' . $today->copy()->addDays(3)->format('d M Y') . '): CRUD data barang, Integrasi form kontak, Laporan transaksi');
        $this->command->info('  H-1  (' . $today->copy()->addDay()->format('d M Y') . '):   Halaman profil & layanan, Modul data produk');
        $this->command->info('  Overdue: Laporan stok, Galeri produk, Modul pesanan, Perbaikan bug validasi Say It Last (' . $today->copy()->subDay()->format('d M Y') . ')');
        $this->command->info('  Done:    Analisis kebutuhan, Desain database, Halaman beranda, Modul login, Analisis Say It Last, Desain DB Say It Last');
    }
}
