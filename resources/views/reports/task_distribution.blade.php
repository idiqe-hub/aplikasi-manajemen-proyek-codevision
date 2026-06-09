@extends('layouts.admin')
@section('title', 'Laporan Distribusi Tugas per Proyek')
@section('page_title', 'Laporan Distribusi Tugas per Proyek')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Laporan Distribusi Task per Project</h6>
      <div class="d-flex">
        <a id="btnPdfTaskDistribution" class="btn btn-sm btn-danger mr-2"
          href="{{ route('reports.task_distribution.pdf', request()->query()) }}">
          <i class="fas fa-file-pdf"></i> Unduh PDF
        </a>

        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
          <i class="fas fa-arrow-left"></i> Kembali
        </a>
      </div>
    </div>

    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Proyek</th>
            <th>Belum Dikerjakan</th>
            <th>Sedang Dikerjakan</th>
            <th>Selesai</th>
            <th>Total Tugas</th>
            <th>Persentase Selesai</th>
          </tr>
        </thead>
        <tbody>
          @forelse($projects as $p)
            @php
              $total = $p->total_count;
              $done = $p->done_count;
              $pct = $total > 0 ? ($done / $total) * 100 : 0;
            @endphp
            <tr>
              <td>{{ ($projects->currentPage() - 1) * $projects->perPage() + $loop->iteration }}</td>
              <td class="font-weight-bold">{{ $p->name }}</td>
              <td>{{ $p->todo_count }}</td>
              <td>{{ $p->in_progress_count }}</td>
              <td>{{ $done }}</td>
              <td class="font-weight-bold">{{ $total }}</td>
              <td>
                <div class="progress" style="height: 20px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%;">
                    {{ number_format($pct, 0) }}%
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4">Tidak ada data project.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{ $projects->links() }}
    </div>
  </div>

  <script>
    (function () {
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Jakarta';
      const btn = document.getElementById('btnPdfTaskDistribution');
      if (!btn) return;

      const url = new URL(btn.href);
      url.searchParams.set('tz', tz);
      btn.href = url.toString();
    })();
  </script>
@endsection
