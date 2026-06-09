<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Notifications\DeadlineApproachingNotification;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTaskDeadlines extends Command
{
    protected $signature   = 'tasks:check-deadline';
    protected $description = 'Cek deadline task dan kirim notifikasi ke Developer (H-3, H-1) dan Admin (overdue).';

    public function handle(): int
    {
        $today = now()->toDateString();
        $h1    = now()->addDay()->toDateString();   // H-1 (besok)
        $h3    = now()->addDays(3)->toDateString(); // H-3 (3 hari lagi)

        // Ambil task yang belum done dan punya deadline
        $tasks = Task::with(['developer.user'])
            ->whereNotNull('deadline')
            ->where('status', '!=', 'done')
            ->get();

        $notifCount = 0;

        foreach ($tasks as $task) {
            // ===== NOTIFIKASI DEVELOPER (H-1 dan H-3) =====
            if ($task->developer?->user) {
                $developerUser = $task->developer->user;

                // H-1
                if ($task->deadline === $h1) {
                    if (! $this->alreadyNotified($developerUser->id, $task->id, 'h-1')) {
                        $developerUser->notify(new DeadlineApproachingNotification($task, 'h-1'));
                        $notifCount++;
                        $this->line("  [H-1] Notif → {$developerUser->name} | Task: {$task->title}");
                    }
                }

                // H-3
                if ($task->deadline === $h3) {
                    if (! $this->alreadyNotified($developerUser->id, $task->id, 'h-3')) {
                        $developerUser->notify(new DeadlineApproachingNotification($task, 'h-3'));
                        $notifCount++;
                        $this->line("  [H-3] Notif → {$developerUser->name} | Task: {$task->title}");
                    }
                }
            }

            // ===== NOTIFIKASI ADMIN (OVERDUE) =====
            if ($task->deadline < $today) {
                // Kirim ke semua admin
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    if (! $this->alreadyNotified($admin->id, $task->id, 'overdue')) {
                        $admin->notify(new TaskOverdueNotification($task));
                        $notifCount++;
                        $this->line("  [OVERDUE] Notif → {$admin->name} | Task: {$task->title}");
                    }
                }
            }
        }

        $this->info("Selesai. Total notifikasi terkirim: {$notifCount}");
        return Command::SUCCESS;
    }

    /**
     * Cek apakah notifikasi untuk task + tipe ini sudah pernah dikirim
     * ke user tertentu (anti-duplikat).
     */
    private function alreadyNotified(int $userId, int $taskId, string $warningType): bool
    {
        return DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->whereJsonContains('data->task_id', $taskId)
            ->whereJsonContains('data->warning_type', $warningType)
            ->exists();
    }
}
