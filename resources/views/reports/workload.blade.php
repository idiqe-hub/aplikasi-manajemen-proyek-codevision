@extends('layouts.admin')
@section('title', 'Report Beban Kerja Developer')
@section('page_title', 'Report Beban Kerja Developer')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Laporan Beban Kerja Developer (Workload)</h6>
      <div class="d-flex">
        <a id="btnPdfWorkload" class="btn btn-sm btn-danger mr-2"
          href="{{ route('reports.workload.pdf', request()->query()) }}">
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
            <th>Developer</th>
            <th>Belum Dikerjakan</th>
            <th>Sedang Dikerjakan</th>
            <th>Total Aktif</th>
            <th>Task Selesai</th>
            <th>Status Beban</th>
          </tr>
        </thead>
        <tbody>
          @forelse($developers as $dev)
            @php
              $aktif = $dev->active_count;
              if ($aktif <= 3) {
                  $status = 'Ringan';
                  $badge = 'success';
              } elseif ($aktif <= 6) {
                  $status = 'Normal';
                  $badge = 'info';
              } else {
                  $status = 'Beban Berlebih';
                  $badge = 'danger';
              }
            @endphp
            <tr>
              <td>{{ ($developers->currentPage() - 1) * $developers->perPage() + $loop->iteration }}</td>
              <td class="font-weight-bold">{{ $dev->name }}</td>
              <td>{{ $dev->todo_count }}</td>
              <td>{{ $dev->in_progress_count }}</td>
              <td class="font-weight-bold">{{ $aktif }}</td>
              <td>{{ $dev->done_count }}</td>
              <td>
                <span class="badge badge-{{ $badge }}">{{ $status }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4">Tidak ada data developer.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{ $developers->links() }}
    </div>
  </div>

  <script>
    (function () {
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Jakarta';
      const btn = document.getElementById('btnPdfWorkload');
      if (!btn) return;

      const url = new URL(btn.href);
      url.searchParams.set('tz', tz);
      btn.href = url.toString();
    })();
  </script>
@endsection
