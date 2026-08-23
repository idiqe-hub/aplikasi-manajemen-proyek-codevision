<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\DeveloperKpi;
use Illuminate\Support\Facades\DB;

class DeveloperDashboardController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $developer = $user->developer;

        if (!$developer) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun developer belum terhubung. Hubungi admin.');
        }

        $today = now()->toDateString();

        // ── Statistik Personal ────────────────────────────────────────────────
        $myTasks   = Task::where('developer_id', $developer->id)->get();
        $totalTodo       = $myTasks->where('status', 'todo')->count();
        $totalInProgress = $myTasks->where('status', 'in_progress')->count();
        $totalDone       = $myTasks->where('status', 'done')->count();
        $totalOverdue    = $myTasks->filter(fn($t) =>
            $t->deadline && $t->deadline < $today && $t->status !== 'done'
        )->count();

        // ── KPI Bulan Ini ─────────────────────────────────────────────────────
        $currentPeriod = now()->format('Y-m');
        $myKpi = DeveloperKpi::where('developer_id', $developer->id)
            ->where('period_month', $currentPeriod)
            ->first();

        // ── Task mendekati deadline (≤ 3 hari) ────────────────────────────────
        $upcomingTasks = Task::with('project')
            ->where('developer_id', $developer->id)
            ->where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', $today)
            ->whereDate('deadline', '<=', now()->addDays(3)->toDateString())
            ->orderBy('deadline')
            ->get();

        // ── Task yang overdue ─────────────────────────────────────────────────
        $overdueTasks = Task::with('project')
            ->where('developer_id', $developer->id)
            ->where('status', '!=', 'done')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', $today)
            ->orderBy('deadline')
            ->get();

        // ── Task aktif (in_progress) ──────────────────────────────────────────
        $activeTasks = Task::with('project')
            ->where('developer_id', $developer->id)
            ->where('status', 'in_progress')
            ->orderBy('deadline')
            ->get();

        // ── Grafik produktivitas 7 hari terakhir ──────────────────────────────
        $startDate = now()->subDays(6)->startOfDay();

        $rawCounts = Task::where('developer_id', $developer->id)
            ->where('status', 'done')
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

        // ── Histori KPI 6 bulan ───────────────────────────────────────────────
        $kpiHistory = DeveloperKpi::where('developer_id', $developer->id)
            ->orderByDesc('period_month')
            ->limit(6)
            ->get();

        return view('dashboard.developer', compact(
            'developer',
            'totalTodo',
            'totalInProgress',
            'totalDone',
            'totalOverdue',
            'myKpi',
            'upcomingTasks',
            'overdueTasks',
            'activeTasks',
            'weeklyLabels',
            'weeklyData',
            'kpiHistory',
            'currentPeriod'
        ));
    }
}
