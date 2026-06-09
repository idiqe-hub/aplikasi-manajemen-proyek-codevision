@extends('layouts.admin')
@section('title', 'Report Tingkat Efisiensi Waktu')
@section('page_title', 'Report Tingkat Efisiensi Waktu')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Laporan Tingkat Efisiensi Waktu</h6>
      <div class="d-flex">
        <a id="btnPdfTimeEfficiency" class="btn btn-sm btn-danger mr-2"
          href="{{ route('reports.time_efficiency.pdf', request()->query()) }}">
          <i class="fas fa-file-pdf"></i> Unduh PDF
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
          <i class="fas fa-arrow-left"></i> Kembali
        </a>
      </div>
    </div>

    <div class="card-body">
      <form method="GET" class="row">
        <div class="col-md-3 form-group">
          <label>Dari Tanggal (Selesai)</label>
          <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3 form-group">
          <label>Sampai Tanggal</label>
          <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-2 form-group d-flex align-items-end">
          <button class="btn btn-primary btn-block">Filter</button>
        </div>
      </form>
    </div>

    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Task</th>
            <th>Developer</th>
            <th>Estimasi (Jam)</th>
            <th>Aktual (Jam)</th>
            <th>Efisiensi (%)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $task)
            @php
              $est = (float) $task->estimated_hours;
              $act = (float) $task->actual_hours;
              
              if ($act <= 0) {
                  $efficiencyStr = '-';
                  $status = 'N/A';
                  $badge = 'secondary';
              } else {
                  $efficiency = ($est / $act) * 100;
                  $efficiencyStr = number_format($efficiency, 2) . '%';
                  
                  if ($efficiency >= 100) {
                      $status = 'Efisien';
                      $badge = 'success';
                  } elseif ($efficiency >= 80) {
                      $status = 'Cukup Efisien';
                      $badge = 'info';
                  } else {
                      $status = 'Kurang Efisien';
                      $badge = 'warning';
                  }
              }
            @endphp
            <tr>
              <td>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</td>
              <td>{{ $task->title }}</td>
              <td>{{ $task->developer?->name ?? '-' }}</td>
              <td>{{ $task->estimated_hours ?? '-' }}</td>
              <td>{{ $task->actual_hours ?? '-' }}</td>
              <td>{{ $efficiencyStr }}</td>
              <td>
                <span class="badge badge-{{ $badge }}">{{ $status }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{ $tasks->links() }}
    </div>
  </div>

  <script>
    (function () {
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Jakarta';
      const btn = document.getElementById('btnPdfTimeEfficiency');
      if (!btn) return;

      const url = new URL(btn.href);
      url.searchParams.set('tz', tz);
      btn.href = url.toString();
    })();
  </script>
@endsection
