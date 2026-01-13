<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Developer;
use Illuminate\Http\Request;

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



    public function create()
    {
        $projects = Project::orderBy('name')->get();
        $developers = Developer::orderBy('name')->get();

        return view('tasks.create', compact('projects', 'developers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required'],
            'developer_id' => ['nullable'], 
        ]);


        if (auth()->user()->role === 'developer') {
            $developer = auth()->user()->developer;

            if (!$developer) {
                abort(403);
            }

            $data['developer_id'] = $developer->id;
        }

        else {
            $request->validate([
                'developer_id' => ['required', 'exists:developers,id'],
            ]);
        }

        Task::create($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil ditambahkan.');
    }


    public function show(Task $task)
    {
        $this->authorizeDeveloper($task);

        return view('tasks.show', compact('task'));
    }


    public function edit(Task $task)
    {
        $projects = Project::orderBy('name')->get();
        $developers = Developer::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects', 'developers'));
    }


    public function update(Request $request, Task $task)
    {
        $this->authorizeDeveloper($task);

        $user = auth()->user();

        if ($user->role === 'admin') {
            $data = $request->validate([
                'project_id'       => ['required', 'exists:projects,id'],
                'developer_id'     => ['required', 'exists:developers,id'],
                'title'            => ['required', 'string', 'max:255'],
                'description'      => ['nullable', 'string'],
                'deadline'         => ['nullable', 'date'],
                'status'           => ['required', 'in:todo,in_progress,done'],
                'progress'         => ['required', 'integer', 'min:0', 'max:100'],
                'estimated_hours'  => ['nullable', 'numeric', 'min:0'],
                'actual_hours'     => ['nullable', 'numeric', 'min:0'],
            ]);
        } else {

            $data = $request->validate([
                'description'      => ['nullable', 'string'],
                'deadline'         => ['nullable', 'date'],
                'status'           => ['required', 'in:todo,in_progress,done'],
                'progress'         => ['required', 'integer', 'min:0', 'max:100'],
                'actual_hours'     => ['nullable', 'numeric', 'min:0'],
            ]);

            $data['developer_id'] = $task->developer_id;
            $data['project_id']   = $task->project_id;
            $data['title']        = $task->title;
            $data['estimated_hours'] = $task->estimated_hours;
        }

        $task->update($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil diperbarui.');
    }



    public function destroy(Task $task)
    {
        $this->authorizeDeveloper($task);

        $task->delete();

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
}
