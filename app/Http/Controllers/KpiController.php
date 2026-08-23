<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\DeveloperKpi;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class KpiController extends Controller
{
    // ─── Index: Daftar semua developer + KPI terbaru (admin only) ─────────────
    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));

        $developers = Developer::with([
            'kpis' => fn($q) => $q->where('period_month', $month),
            'latestKpi',
        ])->withCount([
            'tasks as active_count' => fn($q) => $q->whereIn('status', ['todo', 'in_progress']),
            'tasks as done_count'   => fn($q) => $q->where('status', 'done'),
        ])->orderBy('name')->get();

        // Semua periode yang punya KPI (untuk dropdown filter)
        $availablePeriods = DeveloperKpi::distinct()
            ->orderByDesc('period_month')
            ->pluck('period_month');

        return view('kpi.index', compact('developers', 'month', 'availablePeriods'));
    }

    // ─── Show: Detail KPI 1 developer + histori capaian ─────────────────────
    public function show(Developer $developer)
    {
        $kpiHistory = $developer->kpis()->orderByDesc('period_month')->get();

        // Statistik ringkasan all-time
        $allTimeTasks = Task::where('developer_id', $developer->id)->get();
        $allTimeDone  = $allTimeTasks->where('status', 'done')->count();
        $allTimeTotal = $allTimeTasks->count();

        // KPI terbaru
        $latestKpi = $kpiHistory->first();

        // Data chart: 6 bulan terakhir (untuk line chart)
        $chartData = $this->buildChartData($developer->id, 6);

        return view('kpi.show', compact(
            'developer',
            'kpiHistory',
            'allTimeDone',
            'allTimeTotal',
            'latestKpi',
            'chartData'
        ));
    }

    // ─── Calculate: Kalkulasi/regenerate KPI untuk periode tertentu ──────────
    public function calculate(Request $request)
    {
        $request->validate([
            'period_month' => 'required|date_format:Y-m',
            'developer_id' => 'nullable|exists:developers,id',
        ]);

        $period    = $request->period_month;
        $devId     = $request->developer_id;

        $developers = $devId
            ? Developer::where('id', $devId)->get()
            : Developer::all();

        $count = 0;
        foreach ($developers as $dev) {
            $this->recalculateForDeveloper($dev, $period);
            $count++;
        }

        return redirect()->route('kpi.index', ['month' => $period])
            ->with('success', "KPI berhasil dikalkulasi untuk {$count} developer (periode {$period}).");
    }

    // ─── Helper: Kalkulasi KPI untuk 1 developer 1 periode ───────────────────
    public function recalculateForDeveloper(Developer $developer, string $periodMonth): DeveloperKpi
    {
        // Batas waktu periode
        $startOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $periodMonth)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        // Task yang diassign di periode ini (berdasarkan deadline atau updated_at bulan tsb)
        $tasks = Task::where('developer_id', $developer->id)
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('deadline', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('updated_at', [$startOfMonth, $endOfMonth]);
            })
            ->get();

        $tasksAssigned = $tasks->count();
        $tasksDone     = $tasks->where('status', 'done')->count();

        // Task selesai tepat waktu (done & deadline >= tanggal selesai)
        $tasksOntime = $tasks->filter(function ($t) {
            return $t->status === 'done'
                && $t->deadline
                && $t->updated_at->toDateString() <= $t->deadline;
        })->count();

        $tasksOverdueDone = $tasksDone - $tasksOntime;

        // Jam kerja
        $totalEstimated = $tasks->whereIn('status', ['done'])->sum('estimated_hours') ?? 0;
        $totalActual    = $tasks->whereIn('status', ['done'])->sum('actual_hours') ?? 0;

        // Hitung skor
        $scores = DeveloperKpi::calculateScore(
            $tasksDone,
            $tasksOntime,
            max(1, $tasksAssigned),
            (float) $totalEstimated,
            (float) $totalActual
        );

        return DeveloperKpi::updateOrCreate(
            ['developer_id' => $developer->id, 'period_month' => $periodMonth],
            array_merge($scores, [
                'tasks_assigned'       => $tasksAssigned,
                'tasks_done'           => $tasksDone,
                'tasks_ontime'         => $tasksOntime,
                'tasks_overdue_done'   => max(0, $tasksOverdueDone),
                'total_estimated_hours' => $totalEstimated,
                'total_actual_hours'   => $totalActual,
            ])
        );
    }

    // ─── Helper: Data chart 6 bulan terakhir ─────────────────────────────────
    private function buildChartData(int $developerId, int $months = 6): array
    {
        $labels  = [];
        $scores  = [];
        $grades  = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $period = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->translatedFormat('M Y');

            $kpi = DeveloperKpi::where('developer_id', $developerId)
                ->where('period_month', $period)
                ->first();

            $scores[] = $kpi ? round($kpi->kpi_score, 1) : null;
            $grades[] = $kpi?->grade ?? '-';
        }

        return compact('labels', 'scores', 'grades');
    }

    // ─── PDF Report ────────────────────────────────────────────────────────
    public function pdf(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));

        $developers = Developer::with([
            'kpis' => fn($q) => $q->where('period_month', $month),
            'latestKpi',
        ])->orderBy('name')->get();

        $kopData = $this->kopData($request, "KPI-$month", "Laporan KPI Developer Bulanan", "Periode: " . \Carbon\Carbon::parse($month)->translatedFormat('F Y'));

        $data = array_merge($kopData, [
            'developers' => $developers,
            'month'      => $month,
        ]);

        $pdf = Pdf::loadView('kpi.pdf', $data)->setPaper('a4', 'landscape');

        if (request()->hasAny(['wireframe', 'html_preview'])) {
            return view('kpi.pdf', $data);
        }
        return $pdf->stream("Laporan_KPI_Developer_{$month}.pdf");
    }

    private function resolveTz(Request $request): string
    {
        $tz = $request->query('tz', config('app.timezone', 'Asia/Jakarta'));
        return in_array($tz, \DateTimeZone::listIdentifiers(), true)
            ? $tz
            : config('app.timezone', 'Asia/Jakarta');
    }

    private function kopData(Request $request, string $docTitle, string $reportTitle, string $filters = '', string $keterangan = '')
    {
        $tz = $this->resolveTz($request);
        $logoPath = public_path('sbadmin2/img/logo-pkl.png');
        $logoUri = file_exists($logoPath) ? ('file://' . $logoPath) : null;

        return [
            'docTitle'       => $docTitle,
            'reportTitle'    => $reportTitle,
            'reportSubtitle' => 'Sistem Manajemen Project & Task Developer',
            'printedAt'      => now()->timezone($tz)->format('d-m-Y H:i'),
            'filters'        => $filters,
            'keterangan'     => $keterangan,

            'instansiName'    => 'CV. MAHKOTA BARITO',
            'instansiTagline' => 'CODEVISION.ID Software House & IT Solutions',
            'instansiAddress' => 'Jl. Temanggung Silam RT 002 / RW 004 NO 29 Puruk Cahu, Kec. Murung, Kabupaten Murung Raya Kalimantan Tengah 73911',
            'instansiContact' => 'Email: hello@codevision.id | Web: https://codevision.id',
            'logoPath'        => file_exists($logoPath) ? $logoPath : null,
            'logoUri'         => $logoUri,
            'tz'              => $tz,
        ];
    }
}
