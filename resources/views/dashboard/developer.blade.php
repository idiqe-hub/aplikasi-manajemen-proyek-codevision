@extends('layouts.admin')

@section('title', 'Dashboard Saya — Developer')
@section('page_title', 'Dashboard Developer')

@section('content')
<div class="container-fluid">

  {{-- ─── Header Greeting ─── --}}
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-code mr-2 text-primary"></i>
        Halo, {{ $developer->name }}!
      </h1>
      <p class="mb-0 text-muted">
        {{ now()->translatedFormat('l, d F Y') }} — Berikut ringkasan pekerjaan Anda hari ini.
      </p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('tasks.kanban') }}" class="btn btn-sm btn-primary mr-2">
        <i class="fas fa-columns"></i> Kanban Board
      </a>
      <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-list"></i> Semua Tugas
      </a>
    </div>
  </div>

  {{-- ─── Stat Cards Personal ─── --}}
  <div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Belum Dikerjakan</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTodo }}</div>
            </div>
            <div class="col-auto"><i class="fas fa-clipboard fa-2x text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sedang Dikerjakan</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalInProgress }}</div>
            </div>
            <div class="col-auto"><i class="fas fa-spinner fa-2x text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Selesai</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDone }}</div>
            </div>
            <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      @if($myKpi)
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">KPI Bulan Ini</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ round($myKpi->kpi_score, 1) }}</div>
                <div class="text-muted small mt-1">
                  Grade:
                  <span class="badge {{ \App\Models\DeveloperKpi::gradeBadgeClass($myKpi->grade) }}">
                    {{ $myKpi->grade }} — {{ \App\Models\DeveloperKpi::gradeLabel($myKpi->grade) }}
                  </span>
                </div>
              </div>
              <div class="col-auto"><i class="fas fa-star fa-2x text-gray-300"></i></div>
            </div>
          </div>
        </div>
      @else
        <div class="card border-left-secondary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">KPI Bulan Ini</div>
                <div class="h6 mb-0 text-gray-500">Belum dikalkulasi</div>
                <div class="text-muted small mt-1">Hubungi admin</div>
              </div>
              <div class="col-auto"><i class="fas fa-star fa-2x text-gray-300"></i></div>
            </div>
          </div>
        </div>
      @endif
    </div>

  </div>

  {{-- ─── Grafik + KPI ringkasan ─── --}}
  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card shadow h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar mr-1"></i> Produktivitas Saya (7 Hari Terakhir)
          </h6>
          <span class="text-muted small">Task selesai per hari</span>
        </div>
        <div class="card-body">
          <canvas id="myProductivityChart" height="120"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow h-100">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history mr-1"></i> Histori KPI (6 Bulan)
          </h6>
        </div>
        <div class="card-body p-0">
          @if($kpiHistory->isEmpty())
            <div class="p-4 text-center text-muted small">
              <i class="fas fa-info-circle mr-1"></i>
              Belum ada data KPI. Admin perlu menkalkulasi KPI Anda.
            </div>
          @else
            <div class="list-group list-group-flush">
              @foreach($kpiHistory as $kpi)
                <div class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center">
                  <div>
                    <div class="font-weight-bold small">
                      {{ \Carbon\Carbon::createFromFormat('Y-m', $kpi->period_month)->translatedFormat('F Y') }}
                    </div>
                    <div class="text-muted" style="font-size:0.75rem;">
                      {{ $kpi->tasks_done }} task selesai · {{ round($kpi->kpi_score, 1) }} poin
                    </div>
                  </div>
                  <span class="badge badge-pill {{ \App\Models\DeveloperKpi::gradeBadgeClass($kpi->grade) }}">
                    {{ $kpi->grade }}
                  </span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ─── Task Aktif + Mendekati Deadline ─── --}}
  <div class="row">

    {{-- Task In Progress --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-play-circle mr-1"></i> Sedang Dikerjakan
          </h6>
          <span class="badge badge-primary badge-pill">{{ $activeTasks->count() }}</span>
        </div>
        <div class="card-body p-0">
          @if($activeTasks->isEmpty())
            <div class="p-4 text-center text-muted small">Tidak ada task yang sedang dikerjakan.</div>
          @else
            <div class="list-group list-group-flush">
              @foreach($activeTasks as $t)
                <div class="list-group-item px-3 py-2" id="dev-task-row-{{ $t->id }}">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <a href="{{ route('tasks.show', $t) }}" class="font-weight-bold small text-gray-800">{{ $t->title }}</a>
                    <button class="btn btn-success btn-sm btn-quick-complete ml-2"
                            data-task-id="{{ $t->id }}"
                            data-url="{{ route('tasks.quick-complete', $t) }}"
                            title="Tandai Selesai">
                      <i class="fas fa-check"></i> Selesai
                    </button>
                  </div>
                  <div class="text-muted small mb-1">
                    <i class="fas fa-folder mr-1"></i>{{ $t->project?->name ?? '-' }}
                    @if($t->deadline)
                      <span class="ml-2"><i class="fas fa-calendar mr-1"></i>{{ $t->deadline }}</span>
                    @endif
                  </div>
                  <div class="progress" style="height:6px;border-radius:3px;">
                    <div class="progress-bar bg-primary" style="width:{{ $t->progress }}%"></div>
                  </div>
                  <div class="text-right text-muted" style="font-size:0.72rem;">{{ $t->progress }}%</div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Task Mendekati Deadline --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-clock mr-1"></i> Mendekati Deadline (≤3 Hari)
          </h6>
          <span class="badge badge-warning badge-pill">{{ $upcomingTasks->count() }}</span>
        </div>
        <div class="card-body p-0">
          @if($upcomingTasks->isEmpty())
            <div class="p-4 text-center text-muted small">
              <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada task mendekati deadline.
            </div>
          @else
            <div class="list-group list-group-flush">
              @foreach($upcomingTasks as $t)
                @php $daysLeft = now()->diffInDays($t->deadline, false); @endphp
                <div class="list-group-item px-3 py-2" id="dev-task-row-{{ $t->id }}">
                  <div class="d-flex justify-content-between align-items-start">
                    <a href="{{ route('tasks.show', $t) }}" class="font-weight-bold small text-gray-800">{{ $t->title }}</a>
                    <button class="btn btn-success btn-sm btn-quick-complete ml-2"
                            data-task-id="{{ $t->id }}"
                            data-url="{{ route('tasks.quick-complete', $t) }}"
                            title="Tandai Selesai">
                      <i class="fas fa-check"></i>
                    </button>
                  </div>
                  <div class="text-muted small mb-1">
                    <i class="fas fa-folder mr-1"></i>{{ $t->project?->name ?? '-' }}
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="badge badge-{{ $daysLeft === 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info') }}">
                      {{ $daysLeft === 0 ? 'Hari ini!' : ($daysLeft === 1 ? 'Besok' : $daysLeft . ' hari lagi') }}
                    </span>
                    <span class="text-muted small">{{ $t->deadline }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>

  {{-- ─── Task Overdue ─── --}}
  @if($overdueTasks->isNotEmpty())
  <div class="row">
    <div class="col-12 mb-4">
      <div class="card shadow border-left-danger">
        <div class="card-header d-flex justify-content-between align-items-center bg-danger text-white">
          <h6 class="m-0 font-weight-bold">
            <i class="fas fa-exclamation-circle mr-1"></i> Tugas Terlambat — Perlu Segera Ditangani!
          </h6>
          <span class="badge badge-light badge-pill">{{ $overdueTasks->count() }}</span>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            @foreach($overdueTasks as $t)
              @php $daysLate = now()->diffInDays($t->deadline); @endphp
              <div class="list-group-item px-3 py-2 list-group-item-danger" id="dev-task-row-{{ $t->id }}">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <a href="{{ route('tasks.show', $t) }}" class="font-weight-bold small text-danger">{{ $t->title }}</a>
                    <div class="text-muted small">
                      <i class="fas fa-folder mr-1"></i>{{ $t->project?->name ?? '-' }}
                      <span class="ml-2 text-danger font-weight-bold">
                        <i class="fas fa-calendar-times mr-1"></i>Terlambat {{ $daysLate }} hari (deadline: {{ $t->deadline }})
                      </span>
                    </div>
                  </div>
                  <button class="btn btn-success btn-sm btn-quick-complete ml-2"
                          data-task-id="{{ $t->id }}"
                          data-url="{{ route('tasks.quick-complete', $t) }}"
                          title="Tandai Selesai">
                    <i class="fas fa-check"></i> Selesaikan
                  </button>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  // ─── Grafik Produktivitas Pribadi ─────────────────────────────────────────
  var wLabels = @json($weeklyLabels);
  var wData   = @json($weeklyData);
  new Chart(document.getElementById('myProductivityChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: wLabels,
      datasets: [{
        label: 'Task Selesai',
        data: wData,
        backgroundColor: 'rgba(28, 200, 138, 0.55)',
        borderColor: 'rgba(28, 200, 138, 1)',
        borderWidth: 2,
        borderRadius: 5,
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

  // ─── Quick Complete AJAX ──────────────────────────────────────────────────
  function handleQuickComplete(btn) {
    var taskId = btn.dataset.taskId;
    var url    = btn.dataset.url;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
        // Hapus dari list / tandai selesai
        ['#dev-task-row-' + taskId, '#task-row-' + taskId].forEach(sel => {
          var el = document.querySelector(sel);
          if (el) {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
          }
        });
        if (window.showToast) showToast('success', data.message || 'Task berhasil diselesaikan!');
      } else {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Selesai';
        if (window.showToast) showToast('error', 'Gagal menandai selesai.');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-check"></i> Selesai';
      if (window.showToast) showToast('error', 'Terjadi kesalahan jaringan.');
    });
  }

  document.querySelectorAll('.btn-quick-complete').forEach(btn => {
    btn.addEventListener('click', () => handleQuickComplete(btn));
  });
})();
</script>
@endpush
