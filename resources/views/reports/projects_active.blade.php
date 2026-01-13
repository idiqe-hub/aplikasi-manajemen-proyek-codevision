@extends('layouts.admin')
@section('title', 'Report Project Aktif')
@section('page_title', 'Report Project Aktif')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Project Aktif (planned / on_progress)</h6>
      <div class="d-flex">
        <a id="btnPdfProjectsActive" class="btn btn-sm btn-danger mr-2"
          href="{{ route('reports.projects_active.pdf', request()->query()) }}">
          <i class="fas fa-file-pdf"></i> Unduh PDF
        </a>

        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
          <i class="fas fa-arrow-left"></i> Kembali
        </a>
      </div>
    </div>

    <div class="card-body">
      <form method="GET" class="row">
        <div class="col-md-4 form-group">
          <label>Status Project</label>
          <select name="status" class="form-control">
            <option value="">-- semua --</option>
            <option value="planned" @selected($status === 'planned')>planned</option>
            <option value="on_progress" @selected($status === 'on_progress')>on_progress</option>
            <option value="completed" @selected($status === 'completed')>completed</option>
          </select>
        </div>

        <div class="col-md-2 form-group d-flex align-items-end">
          <button class="btn btn-primary btn-block">Terapkan</button>
        </div>
      </form>
    </div>


    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Project</th>
            <th>Client</th>
            <th>Start</th>
            <th>Deadline</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($projects as $p)
            <tr>
              <td>{{ ($projects->currentPage() - 1) * $projects->perPage() + $loop->iteration }}</td>
              <td class="font-weight-bold">{{ $p->name }}</td>
              <td>{{ $p->client_name ?? '-' }}</td>
              <td>{{ $p->start_date ?? '-' }}</td>
              <td>{{ $p->end_date ?? '-' }}</td>
              <td>
                <span class="badge badge-{{ $p->status === 'on_progress' ? 'warning' : 'secondary' }}">
                  {{ $p->status }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{ $projects->links() }}
    </div>
  </div>

  {{-- Tambah timezone device ke link PDF (untuk DomPDF) --}}
  <script>
    (function () {
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Jakarta';
      const btn = document.getElementById('btnPdfProjectsActive');
      if (!btn) return;

      const url = new URL(btn.href);
      url.searchParams.set('tz', tz);
      btn.href = url.toString();
    })();
  </script>
@endsection