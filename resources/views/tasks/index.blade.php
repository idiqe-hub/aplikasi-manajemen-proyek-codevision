@extends('layouts.admin')
@section('title','Data Task')
@section('page_title','Data Task')

@php
  $statusBadge = fn($s) => $s==='done' ? 'success' : ($s==='in_progress' ? 'warning' : 'secondary');
@endphp

@section('content')
<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Data Task</h6>
    <div>
      <a href="{{ route('tasks.kanban') }}" class="btn btn-secondary btn-sm mr-1">
        <i class="fas fa-columns"></i> Tampilan Kanban
      </a>
      <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Task
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" width="100%">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Judul</th>
            <th>Project</th>
            <th>Developer</th>
            <th>Status</th>
            <th>Progress</th>
            <th>Deadline</th>
            <th style="width:220px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $t)
            <tr>
              <td>{{ ($tasks->currentPage()-1) * $tasks->perPage() + $loop->iteration }}</td>
              <td class="font-weight-bold">{{ $t->title }}</td>
              <td>{{ $t->project?->name ?? '-' }}</td>
              <td>{{ $t->developer?->name ?? '-' }}</td>
              <td><span class="badge badge-{{ $statusBadge($t->status) }}">{{ $t->status }}</span></td>
              <td>
                <div class="progress" style="height: 18px;">
                  <div class="progress-bar" role="progressbar" style="width: {{ $t->progress }}%;">
                    {{ $t->progress }}%
                  </div>
                </div>
              </td>
              <td>{{ $t->deadline ?? '-' }}</td>
              <td>
                <a href="/tasks/{{ $t->id }}" class="btn btn-sm btn-outline-dark">Detail</a>
                <a href="{{ route('tasks.edit',$t) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="{{ route('tasks.destroy',$t) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin hapus task ini?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-4">Data task belum ada.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $tasks->links() }}
    </div>
  </div>
</div>
@endsection
