@extends('layouts.admin')

@section('title', 'KPI & Performa Developer')
@section('page_title', 'KPI & Performa Developer')

@section('content')
<div class="container-fluid">

  {{-- Header --}}
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">KPI & Performa Developer</h1>
      <p class="mb-0 text-muted">Penilaian Key Performance Indicator berdasarkan data task bulanan.</p>
    </div>
  </div>

  {{-- ─── Filter Periode + Kalkulasi ─── --}}
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="row align-items-end">
        <div class="col-md-4">
          <form method="GET" action="{{ route('kpi.index') }}" class="d-flex align-items-end">
            <div class="mr-2 flex-grow-1">
              <label class="small font-weight-bold text-uppercase text-gray-600 mb-1">Filter Periode</label>
              <input type="month" name="month" class="form-control" value="{{ $month }}" max="{{ now()->format('Y-m') }}">
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-filter"></i> Filter
            </button>
          </form>
        </div>
        <div class="col-md-5 mt-3 mt-md-0">
          <form method="POST" action="{{ route('kpi.calculate') }}" class="d-flex align-items-end">
            @csrf
            <input type="hidden" name="period_month" value="{{ $month }}">
            <div class="mr-2">
              <label class="small font-weight-bold text-uppercase text-gray-600 mb-1">Kalkulasi Ulang KPI</label>
              <p class="mb-0 text-muted small">Recalculate otomatis untuk periode <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</strong></p>
            </div>
            <button type="submit" class="btn btn-success" onclick="return confirm('Hitung ulang KPI semua developer untuk periode ini?')">
              <i class="fas fa-calculator"></i> Hitung KPI
            </button>
          </form>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
          @if($availablePeriods->isNotEmpty())
            <label class="small font-weight-bold text-uppercase text-gray-600 mb-1">Periode Tersedia</label>
            <div class="d-flex flex-wrap gap-1">
              @foreach($availablePeriods->take(4) as $p)
                <a href="{{ route('kpi.index', ['month' => $p]) }}"
                   class="badge badge-pill {{ $p === $month ? 'badge-primary' : 'badge-secondary' }} mr-1 mb-1"
                   style="font-size:0.78rem;">
                  {{ \Carbon\Carbon::createFromFormat('Y-m', $p)->translatedFormat('M Y') }}
                </a>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ─── Keterangan Formula ─── --}}
  <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-start">
    <i class="fas fa-info-circle mt-1 mr-3" style="font-size:1.3rem;"></i>
    <div>
      <strong>Formula KPI Score:</strong>
      <span class="ml-1">(<span class="text-success font-weight-bold">40%</span> On-time Rate)
        + (<span class="text-primary font-weight-bold">35%</span> Efisiensi Jam)
        + (<span class="text-warning font-weight-bold">25%</span> Produktivitas)
      </span>
      &nbsp;|&nbsp;
      <strong>Grade:</strong>
      <span class="badge badge-success ml-1">A ≥85</span>
      <span class="badge badge-primary ml-1">B ≥70</span>
      <span class="badge badge-warning ml-1">C ≥55</span>
      <span class="badge badge-danger ml-1">D &lt;55</span>
    </div>
  </div>

  {{-- ─── Tabel KPI Developer ─── --}}
  <div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-star mr-1"></i>
        KPI Developer — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
          <thead class="thead-dark">
            <tr>
              <th>Developer</th>
              <th>Role</th>
              <th class="text-center">Task Selesai</th>
              <th class="text-center">Tepat Waktu</th>
              <th class="text-center" title="(tepat waktu / done) × 100%">On-time Rate<br><small class="font-weight-normal">40%</small></th>
              <th class="text-center" title="(estimated / actual hours) × 100%">Efisiensi Jam<br><small class="font-weight-normal">35%</small></th>
              <th class="text-center" title="(done / assigned) × 100%">Produktivitas<br><small class="font-weight-normal">25%</small></th>
              <th class="text-center">KPI Score</th>
              <th class="text-center">Grade</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($developers as $dev)
              @php $kpi = $dev->kpis->first(); @endphp
              <tr>
                <td>
                  <div class="font-weight-bold">{{ $dev->name }}</div>
                  <div class="text-muted small">{{ $dev->skill ?? '-' }}</div>
                </td>
                <td><span class="badge badge-secondary">{{ $dev->role }}</span></td>

                @if($kpi)
                  <td class="text-center font-weight-bold">{{ $kpi->tasks_done }}</td>
                  <td class="text-center">
                    <span class="text-success">{{ $kpi->tasks_ontime }}</span>
                    @if($kpi->tasks_overdue_done > 0)
                      / <span class="text-danger small">{{ $kpi->tasks_overdue_done }} terlambat</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <div class="progress mr-2" style="width:60px;height:8px;border-radius:4px;">
                        <div class="progress-bar bg-success" style="width:{{ $kpi->on_time_rate }}%"></div>
                      </div>
                      <span class="small">{{ round($kpi->on_time_rate) }}%</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <div class="progress mr-2" style="width:60px;height:8px;border-radius:4px;">
                        <div class="progress-bar bg-primary" style="width:{{ min(100,$kpi->efficiency_rate) }}%"></div>
                      </div>
                      <span class="small">{{ round($kpi->efficiency_rate) }}%</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <div class="progress mr-2" style="width:60px;height:8px;border-radius:4px;">
                        <div class="progress-bar bg-warning" style="width:{{ $kpi->productivity_rate }}%"></div>
                      </div>
                      <span class="small">{{ round($kpi->productivity_rate) }}%</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="h5 font-weight-bold
                      @if($kpi->kpi_score >= 85) text-success
                      @elseif($kpi->kpi_score >= 70) text-primary
                      @elseif($kpi->kpi_score >= 55) text-warning
                      @else text-danger @endif">
                      {{ round($kpi->kpi_score, 1) }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge badge-pill {{ \App\Models\DeveloperKpi::gradeBadgeClass($kpi->grade) }}" style="font-size:1rem;padding:.35em .7em;">
                      {{ $kpi->grade }}
                    </span>
                    <div class="text-muted small mt-1">{{ \App\Models\DeveloperKpi::gradeLabel($kpi->grade) }}</div>
                  </td>
                @else
                  <td colspan="7" class="text-center text-muted py-3">
                    <span class="small"><i class="fas fa-minus mr-1"></i>Belum ada data KPI. Klik "Hitung KPI" di atas.</span>
                  </td>
                @endif

                <td class="text-center">
                  <a href="{{ route('kpi.show', $dev) }}" class="btn btn-info btn-sm" title="Detail & Histori">
                    <i class="fas fa-chart-line"></i>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
