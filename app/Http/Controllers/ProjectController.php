<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'   => ['nullable', 'exists:clients,id'],
            'name'        => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'start_date'  => ['nullable', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'      => ['required', 'in:planned,on_progress,completed'],
            'description' => ['nullable', 'string'],
        ]);

        Project::create($validated);

        // Invalidate cache laporan yang bergantung pada data project
        Cache::forget('dashboard_stats');
        Cache::forget('report_task_distribution');

        // ─── AJAX / Fetch API: kembalikan JSON jika diminta ───
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Project berhasil ditambahkan.',
                'redirect' => route('projects.index'),
            ]);
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil ditambahkan.');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id'   => ['nullable', 'exists:clients,id'],
            'name'        => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'start_date'  => ['nullable', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'      => ['required', 'in:planned,on_progress,completed'],
            'description' => ['nullable', 'string'],
        ]);

        $project->update($validated);

        // Invalidate cache
        Cache::forget('dashboard_stats');
        Cache::forget('report_task_distribution');

        // ─── AJAX / Fetch API: kembalikan JSON jika diminta ───
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Project berhasil diupdate.',
                'redirect' => route('projects.index'),
            ]);
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil diupdate.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        // Invalidate cache
        Cache::forget('dashboard_stats');
        Cache::forget('report_task_distribution');

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }
}