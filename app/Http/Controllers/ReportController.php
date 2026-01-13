<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // 1) Project aktif (planned + on_progress)
   public function projectsActive(Request $request)
{
    $status = $request->query('status');

    $query = Project::query();

    if (!empty($status)) {
        $query->where('status', $status);
    }

    $projects = $query
        ->orderBy('id', 'asc')
        ->paginate(10)
        ->withQueryString();

    return view('reports.projects_active', compact('projects', 'status'));
}



    // 2) Task per developer (filter developer + tanggal)
    public function tasksByDeveloper(Request $request)
    {
        $developers = Developer::orderBy('name')->get();

        $developerId = $request->query('developer_id');
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Task::with(['project', 'developer'])->latest();

        if ($developerId) {
            $query->where('developer_id', $developerId);
        }

        if ($from) $query->whereDate('deadline', '>=', $from);
        if ($to)   $query->whereDate('deadline', '<=', $to);

        $tasks = $query->paginate(10)->withQueryString();

        $summaryQuery = Task::query();

        if ($developerId) $summaryQuery->where('developer_id', $developerId);
        if ($from)        $summaryQuery->whereDate('deadline', '>=', $from);
        if ($to)          $summaryQuery->whereDate('deadline', '<=', $to);

        $summary = $summaryQuery
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('reports.tasks_by_developer', compact('developers', 'tasks', 'developerId', 'from', 'to', 'summary'));
    }

    // 3) Overdue task
    public function tasksOverdue()
    {
        $tasks = Task::with(['project', 'developer'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->where('status', '!=', 'done')
            ->orderBy('deadline') //
            ->paginate(10);

        return view('reports.tasks_overdue', compact('tasks'));
    }

    // 4) Progress per project
    public function projectProgress(Request $request)
    {
        $status = $request->query('status');

        $rows = Project::query()
            ->when($status, fn($q) => $q->where('projects.status', $status))
            ->leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
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
            ->paginate(10)
            ->withQueryString();

        return view('reports.project_progress', compact('rows', 'status'));
    }

    // 5) Estimasi vs realisasi jam
    public function hoursSummary(Request $request)
    {
        $projectId = $request->query('project_id');
        $projects = Project::orderBy('name')->get();

        $tasksQuery = Task::with('project');

        if ($projectId) {
            $tasksQuery->where('project_id', $projectId);
        }

        $tasks = $tasksQuery->latest()->paginate(10)->withQueryString();

        $totalsQuery = Task::query();

        if ($projectId) {
            $totalsQuery->where('project_id', $projectId);
        }

        $totals = $totalsQuery->selectRaw("
            COALESCE(SUM(estimated_hours), 0) as total_estimated,
            COALESCE(SUM(actual_hours), 0) as total_actual
        ")->first();

        $totals ??= (object) ['total_estimated' => 0, 'total_actual' => 0];
        $diff = (int)$totals->total_actual - (int)$totals->total_estimated;

        return view('reports.hours_summary', compact('projects', 'projectId', 'tasks', 'totals', 'diff'));
    }

    // =========================
    // KOP
    // =========================
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

        return [
            'docTitle' => $docTitle,
            'reportTitle' => $reportTitle,
            'reportSubtitle' => 'Sistem Manajemen Project & Task Developer',
            'printedAt' => now()->timezone($tz)->format('d-m-Y H:i'),
            'filters' => $filters,
            'keterangan' => $keterangan,

            'instansiName' => 'CODEVISION.ID',
            'instansiTagline' => 'Software House & IT Solutions',
            'instansiAddress' => 'Jl. Tj. Raya Prumnas Kayu Tangi No.23 RT.20 Blok 4, Sungai Miai, Kec. Banjarmasin Utara, Kota Banjarmasin, Kalimantan Selatan 70123',
            'logoPath' => file_exists(public_path('images/logo-codevision.png')) ? public_path('images/logo-codevision.png') : null,

            'tz' => $tz,
        ];
    }

    // =========================
    // PDF METHODS
    // =========================

    public function pdfProjectsActive(Request $request)
{
    $status = $request->query('status');

    $query = Project::query();

    if (!empty($status)) {
        $query->where('status', $status);
    }

    $projects = $query->orderBy('id', 'asc')->get();

    $filters = 'status: ' . ($status ?: 'semua');

    $data = $this->kopData(
        $request,
        'Laporan Project',
        'Laporan Project',
        $filters,
        'Daftar project berdasarkan status'
    );

    $tz = $data['tz'];

    $pdf = Pdf::loadView(
        'reports.projects_active_pdf',
        array_merge($data, compact('projects'))
    )->setPaper('a4', 'portrait');

    return $pdf->download(
        'Laporan_Project_' . now()->timezone($tz)->format('Y-m-d_His') . '.pdf'
    );
}


    public function pdfTasksByDeveloper(Request $request)
    {
        $developerId = $request->query('developer_id');
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Task::with(['project', 'developer']);

        if ($developerId) $query->where('developer_id', $developerId);
        if ($from)        $query->whereDate('deadline', '>=', $from);
        if ($to)          $query->whereDate('deadline', '<=', $to);


        $tasks = $query->orderBy('id', 'asc')->get();

        $filters = 'developer_id: ' . ($developerId ?: 'semua') . ', deadline: ' . ($from ?: '-') . ' s/d ' . ($to ?: '-');
        $data = $this->kopData($request, 'Laporan Task per Developer', 'Laporan Task per Developer', $filters, 'Daftar task berdasarkan filter');
        $tz = $data['tz'];

        $pdf = Pdf::loadView('reports.tasks_by_developer_pdf', array_merge($data, compact('tasks')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Task_Per_Developer_' . now()->timezone($tz)->format('Y-m-d_His') . '.pdf');
    }

    public function pdfTasksOverdue(Request $request)
    {

        $tasks = Task::with(['project', 'developer'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->where('status', '!=', 'done')
            ->orderBy('id', 'asc')
            ->get();

        $data = $this->kopData($request, 'Laporan Overdue Task', 'Laporan Overdue Task', 'deadline < hari ini & status != done', 'Task yang melewati batas waktu');
        $tz = $data['tz'];

        $pdf = Pdf::loadView('reports.tasks_overdue_pdf', array_merge($data, compact('tasks')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Overdue_Task_' . now()->timezone($tz)->format('Y-m-d_His') . '.pdf');
    }

    public function pdfProjectProgress(Request $request)
    {
        $status = $request->query('status');


        $rows = Project::query()
            ->when($status, fn($q) => $q->where('projects.status', $status))
            ->leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
            ->selectRaw("
                projects.id,
                projects.name,
                projects.status,
                COUNT(tasks.id) as total_tasks,
                SUM(CASE WHEN tasks.status = 'done' THEN 1 ELSE 0 END) as done_tasks,
                COALESCE(AVG(tasks.progress),0) as avg_progress
            ")
            ->groupBy('projects.id', 'projects.name', 'projects.status')
            ->orderBy('projects.id', 'asc')
            ->get();

        $filters = 'status project: ' . ($status ?: 'semua');
        $data = $this->kopData($request, 'Laporan Progress per Project', 'Laporan Progress per Project', $filters, 'Ringkasan hasil olahan (COUNT/DONE/AVG)');
        $tz = $data['tz'];

        $pdf = Pdf::loadView('reports.project_progress_pdf', array_merge($data, compact('rows')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Progress_Per_Project_' . now()->timezone($tz)->format('Y-m-d_His') . '.pdf');
    }

    public function pdfHoursSummary(Request $request)
    {
        $projectId = $request->query('project_id');


        $tasks = Task::with('project')
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->orderBy('id', 'asc')
            ->get();

        $totals = Task::when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->selectRaw("COALESCE(SUM(estimated_hours),0) as total_estimated, COALESCE(SUM(actual_hours),0) as total_actual")
            ->first();

        $filters = 'project_id: ' . ($projectId ?: 'semua');
        $data = $this->kopData($request, 'Laporan Estimasi vs Realisasi Jam', 'Laporan Estimasi vs Realisasi Jam', $filters, 'Perbandingan total estimasi dan realisasi jam');
        $tz = $data['tz'];

        $pdf = Pdf::loadView('reports.hours_summary_pdf', array_merge($data, compact('tasks', 'totals', 'projectId')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Estimasi_vs_Realisasi_' . now()->timezone($tz)->format('Y-m-d_His') . '.pdf');
    }
}
