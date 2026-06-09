@extends('layouts.admin')
@section('page_title', 'Data Project')

@section('title', 'Data Project')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Project</h6>
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Project
        </a>
    </div>


    <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Project</th>
                            <th>Client</th>
                            <th>Start</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $p)
                            <tr>
                                <td>{{ ($projects->currentPage() - 1) * $projects->perPage() + $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td>{{ $p->client?->name ?? ($p->client_name ?? '-') }}</td>
                                <td>{{ $p->start_date ?? '-' }}</td>
                                <td>{{ $p->end_date ?? '-' }}</td>
                                <td>
                                    <span
                                        class="badge
                                    {{ $p->status === 'completed' ? 'bg-success' : ($p->status === 'on_progress' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $p) }}" class="btn btn-sm btn-outline-dark">Detail</a>
                                    <a href="{{ route('projects.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('projects.destroy', $p) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin hapus project ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Data project belum ada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $projects->links() }}
        </div>
    </div>
@endsection