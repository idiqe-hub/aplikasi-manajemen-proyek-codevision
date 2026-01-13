@extends('layouts.admin')
@section('title', 'Report Progress per Project')
@section('page_title', 'Report Progress per Project')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
      <div class="d-flex">
        <a class="btn btn-sm btn-danger mr-2" href="{{ route('reports.project_progress.pdf', request()->query()) }}">
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
  </div>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Ringkasan Progress</h6>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>Project</th>
            <th>Status</th>
            <th>Total Task</th>
            <th>Done</th>
            <th>Avg Progress</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            <tr>
              <td class="font-weight-bold">{{ $r->name }}</td>
              <td>{{ $r->status }}</td>
              <td>{{ $r->total_tasks }}</td>
              <td>{{ $r->done_tasks }}</td>
              <td>{{ number_format($r->avg_progress, 1) }}%</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $rows->links() }}
    </div>
  </div>
@endsection