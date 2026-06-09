@extends('layouts.admin')
@section('title', 'Data Client')
@section('page_title', 'Data Client')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Client</h6>
        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Client
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Perusahaan</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Jumlah Project</th>
                        <th style="width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ ($clients->currentPage() - 1) * $clients->perPage() + $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $client->name }}</td>
                            <td>{{ $client->company ?? '-' }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone ?? '-' }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $client->projects_count ?? $client->projects->count() }} project
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus client {{ $client->name }}? Akun login client juga akan dihapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data client. <a href="{{ route('clients.create') }}">Tambah sekarang</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        {{ $clients->links() }}
    </div>
</div>
@endsection
