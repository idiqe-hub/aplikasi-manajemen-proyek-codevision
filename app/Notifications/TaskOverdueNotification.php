<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id'      => $this->task->id,
            'task_title'   => $this->task->title,
            'deadline'     => $this->task->deadline,
            'warning_type' => 'overdue',
            'developer'    => $this->task->developer?->name ?? '-',
            'message'      => "Task \"{$this->task->title}\" (developer: {$this->task->developer?->name}) sudah melewati deadline ({$this->task->deadline}) dan belum selesai.",
            'url'          => '/tasks/' . $this->task->id,
        ];
    }
}
