@extends('layouts.admin')
@section('title', 'Detail Client — ' . $client->name)
@section('page_title', 'Detail Client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 font-weight-bold text-gray-800">
        <i class="fas fa-user-circle mr-2 text-primary"></i>{{ $client->name }}
    </h4>
    <div>
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-edit"></i> Ubah
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    {{-- Info Client --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card mr-1"></i> Informasi Client
                </h6>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-xs text-uppercase text-muted mb-1">Nama</dt>
                    <dd class="mb-3 font-weight-bold">{{ $client->name }}</dd>

                    <dt class="text-xs text-uppercase text-muted mb-1">Perusahaan</dt>
                    <dd class="mb-3">{{ $client->company ?? '-' }}</dd>

                    <dt class="text-xs text-uppercase text-muted mb-1">Email</dt>
                    <dd class="mb-3">{{ $client->email }}</dd>

                    <dt class="text-xs text-uppercase text-muted mb-1">Telepon</dt>
                    <dd class="mb-3">{{ $client->phone ?? '-' }}</dd>

                    <dt class="text-xs text-uppercase text-muted mb-1">Akun Login</dt>
                    <dd class="mb-0">
                        @if($client->user)
                            <span class="badge badge-success">
                                <i class="fas fa-check mr-1"></i>Terhubung
                            </span>
                        @else
                            <span class="badge badge-danger">Belum ada akun</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Daftar Project --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-folder-open mr-1"></i>
                    Proyek Client ({{ $client->projects->count() }})
                </h6>
                <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Tambah Proyek
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Proyek</th>
                                <th>Status</th>
                                <th>Mulai</th>
                                <th>Tenggat Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->projects as $project)
                                <tr>
                                    <td class="font-weight-bold">{{ $project->name }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $project->status === 'completed' ? 'badge-success' : ($project->status === 'on_progress' ? 'badge-warning' : 'badge-secondary') }}">
                                            {{ $project->status === 'completed' ? 'Selesai' : ($project->status === 'on_progress' ? 'Sedang Berjalan' : 'Direncanakan') }}
                                        </span>
                                    </td>
                                    <td>{{ $project->start_date ?? '-' }}</td>
                                    <td>{{ $project->end_date ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-dark">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        Belum ada project untuk client ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
