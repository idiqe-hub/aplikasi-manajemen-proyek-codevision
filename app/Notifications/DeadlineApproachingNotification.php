<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineApproachingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task   $task,
        public string $warningType  // 'h-3' atau 'h-1'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $daysLeft = $this->warningType === 'h-1' ? 1 : 3;
        $label    = $this->warningType === 'h-1'
            ? 'besok'
            : "dalam {$daysLeft} hari";

        return [
            'task_id'      => $this->task->id,
            'task_title'   => $this->task->title,
            'deadline'     => $this->task->deadline,
            'warning_type' => $this->warningType,
            'message'      => "Deadline task \"{$this->task->title}\" akan berakhir {$label} ({$this->task->deadline}).",
            'url'          => '/tasks/' . $this->task->id,
        ];
    }
}
