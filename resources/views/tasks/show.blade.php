@extends('layouts.admin')
@section('title','Detail Task')
@section('page_title','Detail Task')

@section('content')
<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Detail Task</h6>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
  </div>
  <div class="card-body">
    <p><b>Judul:</b> {{ $task->title }}</p>
    <p><b>Project:</b> {{ $task->project?->name ?? '-' }}</p>
    <p><b>Developer:</b> {{ $task->developer?->name ?? '-' }}</p>
    <p><b>Status:</b> {{ $task->status }}</p>
    <p><b>Progress:</b> {{ $task->progress }}%</p>
    <p><b>Deadline:</b> {{ $task->deadline ?? '-' }}</p>
    <p><b>Estimasi Jam:</b> {{ $task->estimated_hours ?? '-' }}</p>
    <p><b>Realisasi Jam:</b> {{ $task->actual_hours ?? '-' }}</p>
    <p><b>Deskripsi:</b> {{ $task->description ?? '-' }}</p>
  </div>
</div>
@endsection
