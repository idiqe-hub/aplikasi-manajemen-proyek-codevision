<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixNotificationUrls extends Command
{
    protected $signature   = 'notifications:fix-urls';
    protected $description = 'Perbaiki URL absolut di tabel notifications menjadi relative path.';

    public function handle(): int
    {
        $updated = 0;

        DB::table('notifications')->get()->each(function ($notif) use (&$updated) {
            $data = json_decode($notif->data, true);

            // Hanya proses jika url ada dan masih berupa URL absolut
            if (isset($data['url']) && str_starts_with($data['url'], 'http')) {
                $relativePath = parse_url($data['url'], PHP_URL_PATH);

                if ($relativePath) {
                    $data['url'] = $relativePath;
                    DB::table('notifications')
                        ->where('id', $notif->id)
                        ->update(['data' => json_encode($data)]);
                    $updated++;
                    $this->line("  Fixed: {$notif->id} → {$relativePath}");
                }
            }
        });

        $this->info("Selesai. Total notifikasi diperbarui: {$updated}");
        return Command::SUCCESS;
    }
}
