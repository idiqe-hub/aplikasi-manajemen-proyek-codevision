@extends('layouts.admin')

@section('title', 'Detail Proyek')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Detail Proyek</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary">Ubah</a>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card card-body">
    <div class="mb-2"><span class="fw-semibold">Nama:</span> {{ $project->name }}</div>
    <div class="mb-2"><span class="fw-semibold">Client:</span>
        {{ $project->client?->name ?? ($project->client_name ?? '-') }}
        @if($project->client?->company)
            <small class="text-muted">({{ $project->client->company }})</small>
        @endif
    </div>
    <div class="mb-2"><span class="fw-semibold">Tanggal Mulai:</span> {{ $project->start_date ?? '-' }}</div>
    <div class="mb-2"><span class="fw-semibold">Tenggat Waktu:</span> {{ $project->end_date ?? '-' }}</div>
    <div class="mb-2"><span class="fw-semibold">Status:</span> {{ $project->status }}</div>
    <div class="mb-0"><span class="fw-semibold">Deskripsi:</span> {{ $project->description ?? '-' }}</div>
</div>
@endsection
