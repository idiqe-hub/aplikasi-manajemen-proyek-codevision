<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Developer;
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

        // ─── FIX N+1: Grafik 7 hari — dari 7 query terpisah menjadi 1 query GROUP BY ───
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

        return view('dashboard.index', array_merge($dashboardData, compact(
            'overdueTasks',
            'latestTasks',
            'weeklyLabels',
            'weeklyData'
        )));
    }
}
