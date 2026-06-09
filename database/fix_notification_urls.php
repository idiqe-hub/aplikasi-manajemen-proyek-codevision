/**
 * Script untuk update URL di notifikasi lama dari absolute ke relative path.
 * Jalankan di Artisan Tinker: php artisan tinker < database/fix_notification_urls.php
 *
 * Contoh sebelum: {"url":"http://localhost/tasks/5"}
 * Contoh setelah:  {"url":"/tasks/5"}
 */

use Illuminate\Support\Facades\DB;

$updated = 0;

DB::table('notifications')
    ->get()
    ->each(function ($notif) use (&$updated) {
        $data = json_decode($notif->data, true);

        if (isset($data['url']) && str_starts_with($data['url'], 'http')) {
            // Ambil hanya path-nya: /tasks/5
            $parsed = parse_url($data['url'], PHP_URL_PATH);
            if ($parsed) {
                $data['url'] = $parsed;
                DB::table('notifications')
                    ->where('id', $notif->id)
                    ->update(['data' => json_encode($data)]);
                $updated++;
                echo "Updated notif #{$notif->id}: URL → {$parsed}" . PHP_EOL;
            }
        }
    });

echo "Selesai. Total notifikasi diperbarui: {$updated}" . PHP_EOL;
