<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Developer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Guard: client tidak boleh mengakses dashboard admin.
        // Meski route sudah diproteksi middleware role:admin,developer,
        // ini sebagai lapisan kedua yang lebih ramah (redirect vs 403).
        if (auth()->user()->role === 'client') {
            return redirect()->route('client.dashboard');
        }

        $totalProjects   = Project::count();
        $totalTasks      = Task::count();
        $totalDevelopers = Developer::count();

        $overdueCount = Task::whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->where('status', '!=', 'done')
            ->count();

        $doneTasks = Task::where('status', 'done')->count();
        $doneRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

        $overdueTasks = Task::with(['project','developer'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->where('status', '!=', 'done')
            ->orderBy('deadline')
            ->limit(8)
            ->get();

        $latestTasks = Task::with(['project','developer'])
            ->latest('id')
            ->limit(8)
            ->get();

        // Data grafik produktivitas mingguan (7 hari terakhir)
        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $weeklyLabels[] = $day->translatedFormat('D, d M');
            $weeklyData[]   = Task::where('status', 'done')
                ->whereDate('updated_at', $day->toDateString())
                ->count();
        }

        return view('dashboard.index', compact(
            'totalProjects',
            'totalTasks',
            'totalDevelopers',
            'overdueCount',
            'doneTasks',
            'doneRate',
            'overdueTasks',
            'latestTasks',
            'weeklyLabels',
            'weeklyData'
        ));
    }
}
