<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Developer;
use App\Models\TaskActivityLog;
use App\Models\User;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    private function myDeveloperId(): ?int
    {
        return auth()->user()?->developer?->id;
    }

    public function index()
    {
        $query = Task::with(['project', 'developer']);

        $user = auth()->user();


        if (!$user)
            abort(401);


        if ($user->role === 'developer') {
            $developer = $user->developer;

            if (!$developer) {
                return redirect()
                    ->route('pending')
                    ->with('error', 'Akun developer belum terhubung. Hubungi admin untuk approval.');
            }

            $query->where('developer_id', $developer->id);
        }

        $tasks = $query->latest('id')->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function kanban()
    {
        $query = Task::with(['project', 'developer'])->withCount('comments');

        $user = auth()->user();

        if ($user->role === 'developer') {
            $developer = $user->developer;
            if (!$developer) {
                return redirect()->route('pending')
                    ->with('error', 'Akun developer belum terhubung.');
            }
            $query->where('developer_id', $developer->id);
        }

        $tasks = $query->get();

        // Kelompokkan berdasarkan status
        $kanban = [
            'todo'        => $tasks->where('status', 'todo'),
            'in_progress' => $tasks->where('status', 'in_progress'),
            'done'        => $tasks->where('status', 'done'),
        ];

        return view('tasks.kanban', compact('kanban'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeDeveloper($task);

        $request->validate([
            'status' => 'required|in:todo,in_progress,done'
        ]);

        $oldStatus = $task->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return response()->json(['success' => true]);
        }

        $oldProgress = $task->progress;
        $newProgress = $task->progress;

        if ($newStatus === 'done') {
            $newProgress = 100;
        }

        $task->update([
            'status'   => $newStatus,
            'progress' => $newProgress,
        ]);

        // Simpan log aktivitas
        TaskActivityLog::create([
            'task_id'      => $task->id,
            'user_id'      => auth()->id(),
            'old_status'   => $oldStatus,
            'new_status'   => $newStatus,
            'old_progress' => $oldProgress,
            'new_progress' => $newProgress,
            'note'         => 'Memindahkan task di Kanban Board',
        ]);

        return response()->json([
            'success'      => true,
            'new_progress' => $newProgress
        ]);
    }

    /**
     * Quick-complete: Tandai task sebagai selesai langsung dari list/tabel (AJAX).
     * Endpoint: PATCH /tasks/{task}/quick-complete
     */
    public function quickComplete(Task $task)
    {
        $this->authorizeDeveloper($task);

        if ($task->status === 'done') {
            return response()->json(['success' => true, 'message' => 'Task sudah selesai.']);
        }

        $oldStatus   = $task->status;
        $oldProgress = $task->progress;

        $task->update([
            'status'   => 'done',
            'progress' => 100,
        ]);

        TaskActivityLog::create([
            'task_id'      => $task->id,
            'user_id'      => auth()->id(),
            'old_status'   => $oldStatus,
            'new_status'   => 'done',
            'old_progress' => $oldProgress,
            'new_progress' => 100,
            'note'         => 'Ditandai selesai via Quick Complete.',
        ]);

        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil ditandai selesai!',
        ]);
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get();

        // Ambil semua developer + hitung task aktif (todo+in_progress) untuk info workload
        $developers = Developer::withCount([
            'tasks as active_count' => fn($q) => $q->whereIn('status', ['todo', 'in_progress']),
        ])->orderBy('name')->get();

        return view('tasks.create', compact('projects', 'developers'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => ['required', 'exists:projects,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline'    => ['nullable', 'date'],
            'status'      => ['required'],
            'developer_id' => ['nullable'],
        ]);


        if (auth()->user()->role === 'developer') {
            $developer = auth()->user()->developer;

            if (!$developer) {
                abort(403);
            }

            $data['developer_id'] = $developer->id;
        } else {
            $request->validate([
                'developer_id' => ['required', 'exists:developers,id'],
            ]);
        }

        $task = Task::create($data);

        // Kirim notifikasi overdue langsung jika deadline sudah lewat
        $this->sendOverdueNotifIfNeeded($task);

        // Invalidate dashboard cache agar data fresh
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

        // ─── AJAX / Fetch API: kembalikan JSON jika diminta ───
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Task berhasil ditambahkan.',
                'redirect' => route('tasks.index'),
            ]);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil ditambahkan.');
    }


    public function show(Task $task)
    {
        $this->authorizeDeveloper($task);

        $task->load([
            'activityLogs' => function ($query) {
                $query->latest();
            },
            'activityLogs.user',
            'comments' => function ($query) {
                $query->oldest(); // kronologis dari lama ke baru
            },
            'comments.user',
            'project',
            'developer'
        ]);

        return view('tasks.show', compact('task'));
    }


    public function edit(Task $task)
    {
        $projects = Project::orderBy('name')->get();

        // Ambil semua developer + hitung task aktif untuk info workload
        $developers = Developer::withCount([
            'tasks as active_count' => fn($q) => $q->whereIn('status', ['todo', 'in_progress']),
        ])->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects', 'developers'));
    }


    public function update(Request $request, Task $task)
    {
        $this->authorizeDeveloper($task);

        $user = auth()->user();

        if ($user->role === 'admin') {
            $data = $request->validate([
                'project_id'      => ['required', 'exists:projects,id'],
                'developer_id'    => ['required', 'exists:developers,id'],
                'title'           => ['required', 'string', 'max:255'],
                'description'     => ['nullable', 'string'],
                'deadline'        => ['nullable', 'date'],
                'status'          => ['required', 'in:todo,in_progress,done'],
                'progress'        => ['required', 'integer', 'min:0', 'max:100'],
                'estimated_hours' => ['nullable', 'numeric', 'min:0'],
                'actual_hours'    => ['nullable', 'numeric', 'min:0'],
            ]);
        } else {

            $data = $request->validate([
                'description'  => ['nullable', 'string'],
                'deadline'     => ['nullable', 'date'],
                'status'       => ['required', 'in:todo,in_progress,done'],
                'progress'     => ['required', 'integer', 'min:0', 'max:100'],
                'actual_hours' => ['nullable', 'numeric', 'min:0'],
            ]);

            $data['developer_id']    = $task->developer_id;
            $data['project_id']      = $task->project_id;
            $data['title']           = $task->title;
            $data['estimated_hours'] = $task->estimated_hours;
        }

        $oldStatus   = $task->status;
        $oldProgress = $task->progress;

        $task->update($data);

        // Catat log jika ada perubahan status atau progress
        if ($oldStatus !== $task->status || $oldProgress != $task->progress) {
            TaskActivityLog::create([
                'task_id'      => $task->id,
                'user_id'      => auth()->id(),
                'old_status'   => $oldStatus,
                'new_status'   => $task->status,
                'old_progress' => $oldProgress,
                'new_progress' => $task->progress,
                'note'         => 'Diperbarui melalui form edit task',
            ]);
        }

        // Kirim notifikasi overdue langsung jika deadline sudah lewat
        $this->sendOverdueNotifIfNeeded($task);

        // Invalidate dashboard cache agar data fresh
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

        // ─── AJAX / Fetch API: kembalikan JSON jika diminta ───
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Task berhasil diperbarui.',
                'redirect' => route('tasks.index'),
            ]);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil diperbarui.');
    }



    public function destroy(Task $task)
    {
        $this->authorizeDeveloper($task);

        $task->delete();

        // Invalidate dashboard cache agar data fresh
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus.');
    }

    private function authorizeDeveloper(Task $task)
    {
        if (auth()->user()->role === 'developer') {
            $developer = auth()->user()->developer;

            if (!$developer || $task->developer_id !== $developer->id) {
                abort(403);
            }
        }
    }

    /**
     * Kirim notifikasi overdue ke semua admin secara langsung
     * jika task memiliki deadline yang sudah lewat dan belum selesai.
     * Anti-duplikat: tidak mengirim ulang jika sudah pernah dikirim.
     */
    private function sendOverdueNotifIfNeeded(Task $task): void
    {
        // Hanya kirim jika ada deadline, sudah lewat, dan belum done
        if (!$task->deadline || $task->status === 'done') {
            return;
        }

        $today = now()->toDateString();
        if ($task->deadline >= $today) {
            return; // Belum overdue
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            // Cek duplikat: jangan kirim ulang jika sudah ada notifikasi overdue untuk task ini
            $alreadySent = DB::table('notifications')
                ->where('notifiable_id', $admin->id)
                ->where('notifiable_type', 'App\\Models\\User')
                ->whereJsonContains('data->task_id', $task->id)
                ->whereJsonContains('data->warning_type', 'overdue')
                ->exists();

            if (!$alreadySent) {
                $admin->notify(new TaskOverdueNotification($task));
            }
        }
    }
}
