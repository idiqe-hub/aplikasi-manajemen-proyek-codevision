@extends('layouts.admin')
@section('page_title', 'Data Proyek')

@section('title', 'Data Proyek')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Proyek</h6>
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Proyek
        </a>
    </div>


    <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Proyek</th>
                            <th>Client</th>
                            <th>Tanggal Mulai</th>
                            <th>Tenggat Waktu</th>
                            <th>Status</th>
                            <th style="width: 260px;">Aksi</th>
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
                                        {{ $p->status === 'completed' ? 'Selesai' : ($p->status === 'on_progress' ? 'Sedang Berjalan' : 'Direncanakan') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:4px;">
                                        <a href="{{ route('projects.show', $p) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('projects.edit', $p) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Ubah
                                        </a>
                                        <form action="{{ route('projects.destroy', $p) }}" method="POST" class="m-0"
                                            onsubmit="return confirm('Yakin hapus proyek ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Data proyek belum tersedia.</td>
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