<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    /**
     * Dashboard read-only untuk client.
     * Menampilkan hanya project milik client yang sedang login
     * beserta progress dan ringkasan task per project.
     */
    public function index()
    {
        $user   = auth()->user();
        $client = $user->client;

        // Jika user role=client tapi belum punya data client, tampilkan error
        if (!$client) {
            return view('dashboard.client', [
                'client'        => null,
                'projects'      => collect(),
                'totalProjects' => 0,
                'totalTasks'    => 0,
                'doneTasks'     => 0,
                'overdueTasks'  => 0,
            ]);
        }

        // Ambil semua project milik client ini beserta task-nya
        $projects = $client->projects()
            ->withCount([
                'tasks',
                'tasks as todo_count'        => fn($q) => $q->where('status', 'todo'),
                'tasks as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
                'tasks as done_count'        => fn($q) => $q->where('status', 'done'),
                'tasks as overdue_count'     => fn($q) => $q->whereNotNull('deadline')
                                                            ->whereDate('deadline', '<', now()->toDateString())
                                                            ->where('status', '!=', 'done'),
            ])
            ->latest()
            ->get();

        // Hitung rata-rata progress per project dari kolom tasks.progress
        foreach ($projects as $project) {
            $avgProgress = $project->tasks()->avg('progress');
            $project->avg_progress = $avgProgress ? round($avgProgress) : 0;
        }

        // Ringkasan global untuk client ini
        $totalProjects = $projects->count();
        $totalTasks    = $projects->sum('tasks_count');
        $doneTasks     = $projects->sum('done_count');
        $overdueTasks  = $projects->sum('overdue_count');

        return view('dashboard.client', compact(
            'client',
            'projects',
            'totalProjects',
            'totalTasks',
            'doneTasks',
            'overdueTasks'
        ));
    }
}
