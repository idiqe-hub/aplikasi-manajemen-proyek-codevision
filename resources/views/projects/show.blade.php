@extends('layouts.admin')

@section('title', 'Detail Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Detail Project</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary">Edit</a>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card card-body">
    <div class="mb-2"><span class="fw-semibold">Nama:</span> {{ $project->name }}</div>
    <div class="mb-2"><span class="fw-semibold">Client:</span> {{ $project->client_name ?? '-' }}</div>
    <div class="mb-2"><span class="fw-semibold">Start:</span> {{ $project->start_date ?? '-' }}</div>
    <div class="mb-2"><span class="fw-semibold">Deadline:</span> {{ $project->end_date ?? '-' }}</div>
    <div class="mb-2"><span class="fw-semibold">Status:</span> {{ $project->status }}</div>
    <div class="mb-0"><span class="fw-semibold">Deskripsi:</span> {{ $project->description ?? '-' }}</div>
</div>
@endsection
