@extends('layouts.admin')
@section('title', 'Report Produktivitas Mingguan Developer')
@section('page_title', 'Report Produktivitas Mingguan Developer')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Laporan Produktivitas Mingguan Developer</h6>
      <div class="d-flex">
        <a id="btnPdfWeeklyProductivity" class="btn btn-sm btn-danger mr-2"
          href="{{ route('reports.weekly_productivity.pdf', request()->query()) }}">
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
            <th>Developer</th>
            <th>Jumlah Task Selesai</th>
            <th>Rata-rata Waktu Aktual (Jam)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($results as $res)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td class="font-weight-bold">{{ $res->developer?->name ?? '-' }}</td>
              <td>{{ $res->task_count }}</td>
              <td>{{ number_format($res->avg_actual_hours, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <script>
    (function () {
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Jakarta';
      const btn = document.getElementById('btnPdfWeeklyProductivity');
      if (!btn) return;

      const url = new URL(btn.href);
      url.searchParams.set('tz', tz);
      btn.href = url.toString();
    })();
  </script>
@endsection
