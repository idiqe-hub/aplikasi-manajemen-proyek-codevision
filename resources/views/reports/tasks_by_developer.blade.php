@extends('layouts.admin')
@section('title', 'Laporan Tugas per Developer')
@section('page_title', 'Laporan Tugas per Developer')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
            <div class="d-flex">
                <a class="btn btn-sm btn-danger mr-2" href="{{ route('reports.tasks_by_developer.pdf', request()->query()) }}">
                    <i class="fas fa-file-pdf"></i> Unduh PDF
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>


        </div>
        <div class="card-body">
            <form class="row" method="GET">
                <div class="col-md-4 form-group">
                    <label>Developer</label>
                    <select name="developer_id" class="form-control">
                        <option value="">-- semua --</option>
                        @foreach($developers as $d)
                            <option value="{{ $d->id }}" @selected($developerId == $d->id)>{{ $d->name }} ({{ $d->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Dari</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-3 form-group">
                    <label>Sampai</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-2 form-group d-flex align-items-end">
                    <button class="btn btn-primary btn-block">Terapkan</button>
                </div>
            </form>

            <div class="mt-2">
                <span class="badge badge-secondary">Belum Dikerjakan: {{ $summary['todo'] ?? 0 }}</span>
                <span class="badge badge-warning">Sedang Dikerjakan: {{ $summary['in_progress'] ?? 0 }}</span>
                <span class="badge badge-success">Selesai: {{ $summary['done'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hasil</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Tugas</th>
                        <th>Proyek</th>
                        <th>Developer</th>
                        <th>Status</th>
                        <th>Progres</th>
                        <th>Tenggat Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $t)
                        <tr>
                            <td>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $t->title }}</td>
                            <td>{{ $t->project?->name ?? '-' }}</td>
                            <td>{{ $t->developer?->name ?? '-' }}</td>
                            <td><span
                                    class="badge badge-{{ $t->status === 'done' ? 'success' : ($t->status === 'in_progress' ? 'warning' : 'secondary') }}">{{ $t->status === 'done' ? 'Selesai' : ($t->status === 'in_progress' ? 'Sedang Dikerjakan' : 'Belum Dikerjakan') }}</span>
                            </td>
                            <td>{{ $t->progress }}%</td>
                            <td>{{ $t->deadline ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $tasks->links() }}
        </div>
    </div>
@endsection