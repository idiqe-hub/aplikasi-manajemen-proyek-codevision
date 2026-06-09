@extends('layouts.admin')
@section('title','Data Developer')
@section('page_title','Data Developer')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Developer</h6>
        <a href="{{ route('developers.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Developer
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Skill</th>
                    <th style="width:260px;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($developers as $d)
                    <tr>
                        <td>{{ ($developers->currentPage()-1) * $developers->perPage() + $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $d->name }}</td>
                        <td>{{ $d->email }}</td>
                        <td><span class="badge badge-info">{{ $d->role }}</span></td>
                        <td>{{ $d->skill ?? '-' }}</td>
                        <td>
                            <div class="d-flex align-items-center" style="gap:4px;">
                                <a href="{{ route('developers.show',$d) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <a href="{{ route('developers.edit',$d) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Ubah
                                </a>
                                <form action="{{ route('developers.destroy',$d) }}" method="POST" class="m-0"
                                      onsubmit="return confirm('Yakin hapus developer ini?')">
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
                    <tr><td colspan="6" class="text-center py-4">Data developer belum ada.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $developers->links() }}
        </div>
    </div>
</div>
@endsection
