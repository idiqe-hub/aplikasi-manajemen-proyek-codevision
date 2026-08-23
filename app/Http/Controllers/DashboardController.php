<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\DeveloperKpi;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Guard: client tidak boleh mengakses dashboard admin.
        if (auth()->user()->role === 'client') {
            return redirect()->route('client.dashboard');
        }

        // Guard: developer diarahkan ke dashboard personal mereka.
        if (auth()->user()->role === 'developer') {
            return redirect()->route('developer.dashboard');
        }

        // Cache seluruh data dashboard selama 60 detik
        $dashboardData = Cache::remember('dashboard_stats', 60, function () {
            $totalProjects   = Project::count();
            $totalTasks      = Task::count();
            $totalDevelopers = Developer::count();

            $overdueCount = Task::whereNotNull('deadline')
                ->whereDate('deadline', '<', now()->toDateString())
                ->where('status', '!=', 'done')
                ->count();

            $doneTasks = Task::where('status', 'done')->count();
            $doneRate  = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

            return compact(
                'totalProjects',
                'totalTasks',
                'totalDevelopers',
                'overdueCount',
                'doneTasks',
                'doneRate'
            );
        });

        // Data tabel (tidak di-cache agar selalu fresh)
        $overdueTasks = Task::with(['project', 'developer'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->where('status', '!=', 'done')
            ->orderBy('deadline')
            ->limit(8)
            ->get();

        $latestTasks = Task::with(['project', 'developer'])
            ->latest('id')
            ->limit(8)
            ->get();

        // ─── Grafik 7 hari — task selesai per hari ───────────────────────────
        $startDate = now()->subDays(6)->startOfDay();

        $rawCounts = Task::where('status', 'done')
            ->whereDate('updated_at', '>=', $startDate->toDateString())
            ->select(DB::raw('DATE(updated_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day            = now()->subDays($i);
            $weeklyLabels[] = $day->translatedFormat('D, d M');
            $weeklyData[]   = $rawCounts[$day->toDateString()] ?? 0;
        }

        // ─── Progress per Proyek ──────────────────────────────────────────────
        $projectProgress = Project::leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
            ->selectRaw("
                projects.id,
                projects.name,
                projects.status,
                COUNT(tasks.id) as total_tasks,
                SUM(CASE WHEN tasks.status = 'done' THEN 1 ELSE 0 END) as done_tasks,
                COALESCE(AVG(tasks.progress), 0) as avg_progress
            ")
            ->groupBy('projects.id', 'projects.name', 'projects.status')
            ->orderByDesc('avg_progress')
            ->limit(6)
            ->get();

        // ─── Top Developer bulan ini ──────────────────────────────────────────
        $currentPeriod = now()->format('Y-m');

        $topDevelopers = Developer::withCount([
            'tasks as done_this_month' => fn($q) => $q
                ->where('status', 'done')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year),
            'tasks as active_count' => fn($q) => $q->whereIn('status', ['todo', 'in_progress']),
        ])->with([
            'kpis' => fn($q) => $q->where('period_month', $currentPeriod),
        ])
        ->orderByDesc('done_this_month')
        ->limit(5)
        ->get();

        // ─── Distribusi status task ───────────────────────────────────────────
        $taskStatusDist = Task::selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('dashboard.index', array_merge($dashboardData, compact(
            'overdueTasks',
            'latestTasks',
            'weeklyLabels',
            'weeklyData',
            'projectProgress',
            'topDevelopers',
            'taskStatusDist',
            'currentPeriod'
        )));
    }
}
