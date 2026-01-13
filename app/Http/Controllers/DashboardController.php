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

        return view('dashboard.index', compact(
            'totalProjects',
            'totalTasks',
            'totalDevelopers',
            'overdueCount',
            'doneTasks',
            'doneRate',
            'overdueTasks',
            'latestTasks'
        ));
    }
}
