@extends('layouts.admin')
@section('title', 'Report Overdue Task')
@section('page_title', 'Report Overdue Task')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Overdue Task (deadline lewat & belum done)</h6>
      <div class="d-flex">
        <a class="btn btn-sm btn-danger mr-2" href="{{ route('reports.tasks_overdue.pdf') }}">
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
            <th>Task</th>
            <th>Project</th>
            <th>Developer</th>
            <th>Status</th>
            <th>Deadline</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $t)
            <tr>
              <td>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</td>
              <td class="font-weight-bold">{{ $t->title }}</td>
              <td>{{ $t->project?->name ?? '-' }}</td>
              <td>{{ $t->developer?->name ?? '-' }}</td>
              <td><span class="badge badge-danger">{{ $t->status }}</span></td>
              <td class="text-danger font-weight-bold">{{ $t->deadline }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">Tidak ada overdue task.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $tasks->links() }}
    </div>
  </div>
@endsection