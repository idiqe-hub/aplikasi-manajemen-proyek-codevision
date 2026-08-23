@extends('layouts.admin')

@section('title', 'Detail KPI — ' . $developer->name)
@section('page_title', 'Detail KPI Developer')

@section('content')
<div class="container-fluid">

  {{-- Breadcrumb --}}
  <nav class="mb-3">
    <ol class="breadcrumb bg-transparent p-0">
      <li class="breadcrumb-item"><a href="{{ route('kpi.index') }}">KPI</a></li>
      <li class="breadcrumb-item active">{{ $developer->name }}</li>
    </ol>
  </nav>

  {{-- ─── Info Developer ─── --}}
  <div class="row mb-4">
    <div class="col-md-4 mb-4 mb-md-0">
      <div class="card shadow h-100">
        <div class="card-body text-center py-4">
          <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
               style="width:80px;height:80px;font-size:2rem;color:#fff;">
            {{ strtoupper(substr($developer->name, 0, 1)) }}
          </div>
          <h5 class="font-weight-bold text-gray-800">{{ $developer->name }}</h5>
          <span class="badge badge-secondary mb-2">{{ $developer->role }}</span>
          <p class="text-muted small mb-2">{{ $developer->skill ?? 'Skill belum diisi' }}</p>
          <p class="text-muted small mb-0">{{ $developer->email }}</p>
          <hr>
          <div class="row text-center">
            <div class="col-6">
              <div class="h5 font-weight-bold text-success">{{ $allTimeDone }}</div>
              <div class="small text-muted">Total Selesai</div>
            </div>
            <div class="col-6">
              <div class="h5 font-weight-bold text-primary">{{ $allTimeTotal }}</div>
              <div class="small text-muted">Total Ditugaskan</div>
            </div>
          </div>
          @if($latestKpi)
            <hr>
            <div class="py-2">
              <div class="text-muted small mb-1">KPI Terbaru ({{ \Carbon\Carbon::createFromFormat('Y-m', $latestKpi->period_month)->translatedFormat('F Y') }})</div>
              <span class="h3 font-weight-bold
                @if($latestKpi->kpi_score >= 85) text-success
                @elseif($latestKpi->kpi_score >= 70) text-primary
                @elseif($latestKpi->kpi_score >= 55) text-warning
                @else text-danger @endif">
                {{ round($latestKpi->kpi_score, 1) }}
              </span>
              <div class="mt-1">
                <span class="badge badge-pill {{ \App\Models\DeveloperKpi::gradeBadgeClass($latestKpi->grade) }}" style="font-size:1rem;">
                  {{ $latestKpi->grade }} — {{ \App\Models\DeveloperKpi::gradeLabel($latestKpi->grade) }}
                </span>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Grafik Histori KPI --}}
    <div class="col-md-8">
      <div class="card shadow h-100">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-line mr-1"></i> Tren KPI — 6 Bulan Terakhir
          </h6>
        </div>
        <div class="card-body">
          @if($kpiHistory->isEmpty())
            <div class="text-center text-muted py-5">
              <i class="fas fa-chart-line fa-3x mb-3 text-gray-300"></i>
              <p>Belum ada data KPI. Kalkulasi KPI dari halaman daftar KPI.</p>
            </div>
          @else
            <canvas id="kpiTrendChart" height="100"></canvas>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ─── Histori Capaian per Bulan ─── --}}
  <div class="card shadow">
    <div class="card-header">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-history mr-1"></i> Histori Capaian per Bulan
      </h6>
    </div>
    <div class="card-body p-0">
      @if($kpiHistory->isEmpty())
        <div class="p-5 text-center text-muted">
          <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i>
          <p>Belum ada histori KPI untuk developer ini.</p>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-bordered table-hover mb-0">
            <thead class="thead-dark">
              <tr>
                <th>Periode</th>
                <th class="text-center">Task Ditugaskan</th>
                <th class="text-center">Task Selesai</th>
                <th class="text-center">Tepat Waktu</th>
                <th class="text-center">On-time Rate<br><small class="font-weight-normal">(40%)</small></th>
                <th class="text-center">Efisiensi Jam<br><small class="font-weight-normal">(35%)</small></th>
                <th class="text-center">Produktivitas<br><small class="font-weight-normal">(25%)</small></th>
                <th class="text-center">KPI Score</th>
                <th class="text-center">Grade</th>
              </tr>
            </thead>
            <tbody>
              @foreach($kpiHistory as $kpi)
                <tr>
                  <td class="font-weight-bold">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $kpi->period_month)->translatedFormat('F Y') }}
                  </td>
                  <td class="text-center">{{ $kpi->tasks_assigned }}</td>
                  <td class="text-center font-weight-bold text-success">{{ $kpi->tasks_done }}</td>
                  <td class="text-center">
                    {{ $kpi->tasks_ontime }}
                    @if($kpi->tasks_overdue_done > 0)
                      <span class="text-danger small">(+{{ $kpi->tasks_overdue_done }} terlambat)</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <div class="progress mr-2" style="width:55px;height:6px;border-radius:3px;">
                        <div class="progress-bar bg-success" style="width:{{ $kpi->on_time_rate }}%"></div>
                      </div>
                      <span class="small">{{ round($kpi->on_time_rate) }}%</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <div class="progress mr-2" style="width:55px;height:6px;border-radius:3px;">
                        <div class="progress-bar bg-primary" style="width:{{ min(100,$kpi->efficiency_rate) }}%"></div>
                      </div>
                      <span class="small">{{ round($kpi->efficiency_rate) }}%</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <div class="progress mr-2" style="width:55px;height:6px;border-radius:3px;">
                        <div class="progress-bar bg-warning" style="width:{{ $kpi->productivity_rate }}%"></div>
                      </div>
                      <span class="small">{{ round($kpi->productivity_rate) }}%</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="font-weight-bold h6
                      @if($kpi->kpi_score >= 85) text-success
                      @elseif($kpi->kpi_score >= 70) text-primary
                      @elseif($kpi->kpi_score >= 55) text-warning
                      @else text-danger @endif">
                      {{ round($kpi->kpi_score, 1) }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge badge-pill {{ \App\Models\DeveloperKpi::gradeBadgeClass($kpi->grade) }}">
                      {{ $kpi->grade }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  var chartData = @json($chartData);
  if (!chartData.labels || !chartData.labels.length) return;

  var ctx = document.getElementById('kpiTrendChart');
  if (!ctx) return;

  new Chart(ctx.getContext('2d'), {
    type: 'line',
    data: {
      labels: chartData.labels,
      datasets: [{
        label: 'KPI Score',
        data: chartData.scores,
        borderColor: '#4e73df',
        backgroundColor: 'rgba(78,115,223,0.12)',
        borderWidth: 2.5,
        pointRadius: 5,
        pointBackgroundColor: '#fff',
        pointBorderColor: '#4e73df',
        pointBorderWidth: 2,
        tension: 0.3,
        spanGaps: true,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ' KPI: ' + (ctx.parsed.y !== null ? ctx.parsed.y : '-'),
            afterLabel: (ctx) => {
              var grade = chartData.grades[ctx.dataIndex];
              return grade && grade !== '-' ? ' Grade: ' + grade : '';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: false,
          min: 0,
          max: 100,
          ticks: { stepSize: 10 },
          grid: { color: 'rgba(0,0,0,0.05)' }
        },
        x: { grid: { display: false } }
      }
    }
  });
})();
</script>
@endpush
