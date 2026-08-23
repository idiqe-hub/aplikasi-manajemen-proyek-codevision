@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid">

  {{-- Header --}}
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
      <p class="mb-0 text-muted">Gambaran menyeluruh aktivitas proyek & tim developer.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('kpi.index') }}" class="btn btn-sm btn-success mr-2">
        <i class="fas fa-star"></i> KPI & Performa
      </a>
      <a href="{{ route('reports.index') }}" class="btn btn-sm btn-danger">
        <i class="fas fa-file-pdf"></i> Laporan (PDF)
      </a>
    </div>
  </div>

  {{-- ─── Stat Cards ─── --}}
  <div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Proyek</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProjects }}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-folder-open fa-2x text-gray-300"></i>
            </div>
          </div>
          <div class="mt-3">
            <a href="{{ route('projects.index') }}" class="small">Lihat data <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tugas</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTasks }}</div>
              <div class="text-muted small mt-1">Selesai: {{ $doneTasks }} ({{ $doneRate }}%)</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-tasks fa-2x text-gray-300"></i>
            </div>
          </div>
          <div class="progress progress-sm mt-2">
            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $doneRate }}%"
                 aria-valuenow="{{ $doneRate }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div class="mt-2">
            <a href="{{ route('tasks.index') }}" class="small">Lihat data <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Developer</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDevelopers }}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-users fa-2x text-gray-300"></i>
            </div>
          </div>
          <div class="mt-3">
            <a href="{{ route('developers.index') }}" class="small">Lihat data <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-danger shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Terlambat</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overdueCount }}</div>
              <div class="text-muted small mt-1">Tenggat lewat & belum selesai</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
            </div>
          </div>
          <div class="mt-3">
            <a href="{{ route('reports.tasks_overdue') }}" class="small text-danger">Lihat report <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- ─── Grafik Produktivitas + Distribusi Status ─── --}}
  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card shadow h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar mr-1"></i> Produktivitas Mingguan (7 Hari Terakhir)
          </h6>
          <span class="text-muted small">Task selesai per hari</span>
        </div>
        <div class="card-body">
          <canvas id="weeklyProductivityChart" height="120"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow h-100">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-pie mr-1"></i> Distribusi Status Task
          </h6>
        </div>
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <canvas id="taskStatusChart" style="max-height:200px"></canvas>
          <div class="mt-3 w-100">
            @php
              $statusLabels = ['todo' => ['label'=>'Belum Dikerjakan','color'=>'#858796'], 'in_progress' => ['label'=>'Sedang Dikerjakan','color'=>'#4e73df'], 'done' => ['label'=>'Selesai','color'=>'#1cc88a']];
            @endphp
            @foreach($statusLabels as $key => $info)
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span><span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:{{ $info['color'] }}"></span>{{ $info['label'] }}</span>
                <strong>{{ $taskStatusDist[$key] ?? 0 }}</strong>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ─── Progress per Proyek ─── --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-project-diagram mr-1"></i> Progress per Proyek
          </h6>
          <a href="{{ route('reports.project_progress') }}" class="btn btn-sm btn-outline-primary">Laporan Lengkap</a>
        </div>
        <div class="card-body">
          @forelse($projectProgress as $proj)
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-semibold text-gray-700 small">{{ $proj->name }}</span>
                <div>
                  @php
                    $badgeClass = match($proj->status) {
                      'completed'   => 'badge-success',
                      'on_progress' => 'badge-primary',
                      default       => 'badge-secondary',
                    };
                    $badgeLabel = match($proj->status) {
                      'completed'   => 'Selesai',
                      'on_progress' => 'Berjalan',
                      default       => 'Direncanakan',
                    };
                  @endphp
                  <span class="badge {{ $badgeClass }} badge-pill mr-2">{{ $badgeLabel }}</span>
                  <span class="text-muted small">{{ $proj->done_tasks }}/{{ $proj->total_tasks }} task</span>
                  <strong class="ml-2">{{ round($proj->avg_progress) }}%</strong>
                </div>
              </div>
              <div class="progress" style="height:10px;border-radius:5px;">
                <div class="progress-bar
                  @if($proj->avg_progress >= 80) bg-success @elseif($proj->avg_progress >= 50) bg-primary @else bg-warning @endif"
                  role="progressbar"
                  style="width: {{ round($proj->avg_progress) }}%; transition: width 1s ease;"
                  aria-valuenow="{{ round($proj->avg_progress) }}" aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
            </div>
          @empty
            <p class="text-muted text-center mb-0">Belum ada data proyek.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  {{-- ─── Top Developer + Tabel Overdue/Latest ─── --}}
  <div class="row">

    {{-- Top Developer Bulan Ini --}}
    <div class="col-lg-4 mb-4">
      <div class="card shadow h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-success">
            <i class="fas fa-trophy mr-1"></i> Top Developer Bulan Ini
          </h6>
          <a href="{{ route('kpi.index', ['month' => $currentPeriod]) }}" class="btn btn-sm btn-outline-success">KPI</a>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            @forelse($topDevelopers as $i => $dev)
              @php
                $kpiRecord = $dev->kpis->first();
                $medal = match($i) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => '🔹' };
              @endphp
              <a href="{{ route('kpi.show', $dev) }}" class="list-group-item list-group-item-action d-flex align-items-center px-3 py-2">
                <span class="mr-2" style="font-size:1.2rem;">{{ $medal }}</span>
                <div class="flex-grow-1">
                  <div class="font-weight-bold small text-gray-800">{{ $dev->name }}</div>
                  <div class="text-muted" style="font-size:0.75rem;">{{ $dev->done_this_month }} task selesai bulan ini</div>
                </div>
                @if($kpiRecord)
                  <span class="badge badge-pill {{ \App\Models\DeveloperKpi::gradeBadgeClass($kpiRecord->grade) }} ml-2">
                    {{ $kpiRecord->grade }}
                  </span>
                @endif
              </a>
            @empty
              <div class="list-group-item text-muted text-center small">Belum ada data bulan ini.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    {{-- Overdue Table --}}
    <div class="col-lg-8 mb-4">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-danger">Tugas Terlambat (Top 8)</h6>
          <a href="{{ route('reports.tasks_overdue') }}" class="btn btn-sm btn-danger">
            <i class="fas fa-eye"></i> Buka Report
          </a>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th>Tugas</th>
                <th>Proyek</th>
                <th>Developer</th>
                <th class="text-center">Tenggat</th>
              </tr>
            </thead>
            <tbody>
              @forelse($overdueTasks as $t)
                <tr>
                  <td><a href="{{ route('tasks.show', $t) }}" class="text-gray-800">{{ $t->title }}</a></td>
                  <td>{{ $t->project?->name ?? '-' }}</td>
                  <td>{{ $t->developer?->name ?? '-' }}</td>
                  <td class="text-center text-danger font-weight-bold">{{ $t->deadline }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-3"><i class="fas fa-check-circle text-success mr-1"></i>Tidak ada tugas yang terlambat.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  {{-- ─── Tugas Terbaru ─── --}}
  <div class="row">
    <div class="col-12 mb-4">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Tugas Terbaru (Top 8)</h6>
          <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-list"></i> Semua Tugas
          </a>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th>Tugas</th>
                <th>Proyek</th>
                <th>Developer</th>
                <th class="text-center">Status</th>
                <th class="text-center">Progres</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestTasks as $t)
                <tr id="task-row-{{ $t->id }}">
                  <td><a href="{{ route('tasks.show', $t) }}" class="text-gray-800">{{ $t->title }}</a></td>
                  <td>{{ $t->project?->name ?? '-' }}</td>
                  <td>{{ $t->developer?->name ?? '-' }}</td>
                  <td class="text-center">
                    @php
                      $sc = match($t->status){ 'done'=>'badge-success','in_progress'=>'badge-primary',default=>'badge-secondary'};
                      $sl = match($t->status){ 'done'=>'Selesai','in_progress'=>'Dikerjakan',default=>'Todo'};
                    @endphp
                    <span class="badge {{ $sc }}">{{ $sl }}</span>
                  </td>
                  <td class="text-center">{{ $t->progress }}%</td>
                  <td class="text-center">
                    @if($t->status !== 'done')
                      <button class="btn btn-success btn-sm btn-quick-complete"
                              data-task-id="{{ $t->id }}"
                              data-url="{{ route('tasks.quick-complete', $t) }}"
                              title="Tandai Selesai">
                        <i class="fas fa-check"></i>
                      </button>
                    @else
                      <span class="text-success small"><i class="fas fa-check-circle"></i></span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada task.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  // ─── Chart 1: Weekly Productivity ────────────────────────────────────────
  var wLabels = @json($weeklyLabels);
  var wData   = @json($weeklyData);
  new Chart(document.getElementById('weeklyProductivityChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: wLabels,
      datasets: [{
        label: 'Task Selesai',
        data: wData,
        backgroundColor: 'rgba(78, 115, 223, 0.55)',
        borderColor: 'rgba(78, 115, 223, 1)',
        borderWidth: 2,
        borderRadius: 5,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.y + ' task selesai' } }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.05)' } },
        x: { grid: { display: false } }
      }
    }
  });

  // ─── Chart 2: Task Status Doughnut ────────────────────────────────────────
  var statusData = @json(array_values($taskStatusDist));
  @php
    $orderedKeys = ['todo','in_progress','done'];
    $orderedData = array_map(fn($k) => $taskStatusDist[$k] ?? 0, $orderedKeys);
  @endphp
  new Chart(document.getElementById('taskStatusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: ['Belum Dikerjakan', 'Sedang Dikerjakan', 'Selesai'],
      datasets: [{
        data: @json($orderedData),
        backgroundColor: ['#858796','#4e73df','#1cc88a'],
        borderWidth: 2,
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true,
      cutout: '70%',
      plugins: { legend: { display: false } }
    }
  });

  // ─── Quick Complete AJAX ──────────────────────────────────────────────────
  document.querySelectorAll('.btn-quick-complete').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var taskId  = this.dataset.taskId;
      var url     = this.dataset.url;
      var btnEl   = this;

      btnEl.disabled = true;
      btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      fetch(url, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          var row = document.getElementById('task-row-' + taskId);
          if (row) {
            row.querySelector('td:nth-child(4)').innerHTML = '<span class="badge badge-success">Selesai</span>';
            row.querySelector('td:nth-child(5)').textContent = '100%';
            row.querySelector('td:nth-child(6)').innerHTML = '<span class="text-success small"><i class="fas fa-check-circle"></i></span>';
            row.classList.add('table-success');
          }
          if(window.showToast) showToast('success', data.message || 'Task berhasil diselesaikan!');
        } else {
          btnEl.disabled = false;
          btnEl.innerHTML = '<i class="fas fa-check"></i>';
          if(window.showToast) showToast('error', 'Gagal menandai selesai.');
        }
      })
      .catch(() => {
        btnEl.disabled = false;
        btnEl.innerHTML = '<i class="fas fa-check"></i>';
        if(window.showToast) showToast('error', 'Terjadi kesalahan jaringan.');
      });
    });
  });
})();
</script>
@endpush
