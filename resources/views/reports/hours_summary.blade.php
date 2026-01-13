@extends('layouts.admin')
@section('title', 'Report Estimasi vs Realisasi')
@section('page_title', 'Report Estimasi vs Realisasi Jam')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
      <div class="d-flex">
        <a class="btn btn-sm btn-danger mr-2" href="{{ route('reports.hours_summary.pdf', request()->query()) }}">
          <i class="fas fa-file-pdf"></i> Unduh PDF
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
          <i class="fas fa-arrow-left"></i> Kembali
        </a>
      </div>

    </div>
    <div class="card-body">
      <form method="GET" class="row">
        <div class="col-md-6 form-group">
          <label>Project</label>
          <select name="project_id" class="form-control">
            <option value="">-- semua project --</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}" @selected($projectId == $p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 form-group d-flex align-items-end">
          <button class="btn btn-primary btn-block">Terapkan</button>
        </div>
      </form>

      <div class="mt-2">
        <span class="badge badge-info">Total Estimasi: {{ $totals->total_estimated }} jam</span>
        <span class="badge badge-success">Total Realisasi: {{ $totals->total_actual }} jam</span>
        <span class="badge badge-{{ $diff > 0 ? 'danger' : 'secondary' }}">Selisih: {{ $diff }} jam</span>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Daftar Task</h6>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Task</th>
            <th>Project</th>
            <th>Estimasi</th>
            <th>Realisasi</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $t)
            <tr>
              <td>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</td>
              <td class="font-weight-bold">{{ $t->title }}</td>
              <td>{{ $t->project?->name ?? '-' }}</td>
              <td>{{ $t->estimated_hours ?? 0 }}</td>
              <td>{{ $t->actual_hours ?? 0 }}</td>
              <td>{{ $t->status }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $tasks->links() }}
    </div>
  </div>
@endsection