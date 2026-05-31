<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskActivityLog;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Task $task)
    {
        // Authorization: 
        // 1. Client tidak boleh komentar
        if (auth()->user()->role === 'client') {
            abort(403, 'Akses ditolak.');
        }

        // 2. Developer hanya boleh komentar di task miliknya sendiri
        if (auth()->user()->role === 'developer') {
            $developer = auth()->user()->developer;
            if (!$developer || $task->developer_id !== $developer->id) {
                abort(403, 'Anda hanya dapat mengomentari task Anda sendiri.');
            }
        }

        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        // Simpan komentar
        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        // Simpan catatan ke activity log
        TaskActivityLog::create([
            'task_id'      => $task->id,
            'user_id'      => auth()->id(),
            'old_status'   => $task->status,
            'new_status'   => $task->status,
            'old_progress' => $task->progress,
            'new_progress' => $task->progress,
            'note'         => 'Menambahkan komentar pada task',
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
