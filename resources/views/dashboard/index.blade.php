@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid">

  {{-- Header --}}
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
      <p class="mb-0 text-muted">Ringkasan aktivitas Project & Task.</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-danger">
      <i class="fas fa-file-pdf"></i> Laporan (PDF)
    </a>
  </div>

  {{-- Cards --}}
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

          {{-- mini progress --}}
          <div class="progress progress-sm mt-3">
            <div class="progress-bar" role="progressbar" style="width: {{ $doneRate }}%"
                 aria-valuenow="{{ $doneRate }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>

          <div class="mt-3">
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
              <div class="text-muted small mt-1">Tenggat waktu lewat & belum selesai</div>
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

  {{-- Grafik Produktivitas Mingguan --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar mr-1"></i> Produktivitas Mingguan (7 Hari Terakhir)
          </h6>
          <span class="text-muted small">Task selesai per hari</span>
        </div>
        <div class="card-body">
          <canvas id="weeklyProductivityChart" height="80"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Tables --}}
  <div class="row">

    {{-- Overdue Table --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-danger">Tugas Terlambat (Top 8)</h6>
          <a href="{{ route('reports.tasks_overdue') }}" class="btn btn-sm btn-danger">
            <i class="fas fa-eye"></i> Buka Report
          </a>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th>Tugas</th>
                <th>Proyek</th>
                <th>Developer</th>
                <th class="text-center">Tenggat Waktu</th>
              </tr>
            </thead>
            <tbody>
              @forelse($overdueTasks as $t)
                <tr>
                  <td>{{ $t->title }}</td>
                  <td>{{ $t->project?->name ?? '-' }}</td>
                  <td>{{ $t->developer?->name ?? '-' }}</td>
                  <td class="text-center">{{ $t->deadline }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted">Tidak ada tugas yang terlambat.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Latest Tasks --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Tugas Terbaru (Top 8)</h6>
          <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-list"></i> Data Tugas
          </a>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th>Tugas</th>
                <th>Proyek</th>
                <th class="text-center">Status</th>
                <th class="text-center">Progres</th>
              </tr>
            </thead>
            <tbody>
              @forelse($latestTasks as $t)
                <tr>
                  <td>{{ $t->title }}</td>
                  <td>{{ $t->project?->name ?? '-' }}</td>
                  <td class="text-center">{{ $t->status === 'done' ? 'Selesai' : ($t->status === 'in_progress' ? 'Sedang Dikerjakan' : 'Belum Dikerjakan') }}</td>
                  <td class="text-center">{{ $t->progress }}%</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada task.</td></tr>
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
    var labels = @json($weeklyLabels);
    var data   = @json($weeklyData);
    var ctx    = document.getElementById('weeklyProductivityChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Task Selesai',
          data: data,
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
          tooltip: {
            callbacks: {
              label: function(ctx) { return ' ' + ctx.parsed.y + ' task selesai'; }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1, precision: 0 },
            grid: { color: 'rgba(0,0,0,0.05)' }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  })();
</script>
@endpush
