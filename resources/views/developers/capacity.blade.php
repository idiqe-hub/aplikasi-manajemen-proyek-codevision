@extends('layouts.admin')
@section('title', 'Kapasitas Developer')
@section('page_title', 'Kapasitas Developer')

@section('content')

{{-- ============================================================
     HEADER
     ============================================================ --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Kapasitas Developer</h1>
        <p class="mb-0 text-muted">Pantau beban kerja setiap developer secara real-time.</p>
    </div>
    <a href="{{ route('developers.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-users mr-1"></i> Kelola Developer
    </a>
</div>

{{-- ============================================================
     STAT CARDS RINGKASAN
     ============================================================ --}}
<div class="row mb-4">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Developer</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['total_developer'] }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Beban Ringan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['ringan'] }}</div>
                        <div class="text-muted small">0 – 3 task aktif</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Beban Normal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['normal'] }}</div>
                        <div class="text-muted small">4 – 6 task aktif</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-minus-circle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Beban Berlebih</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['overload'] }}</div>
                        <div class="text-muted small">7+ task aktif</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-fire fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ============================================================
     TABEL KAPASITAS
     ============================================================ --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-1"></i> Detail Kapasitas per Developer
        </h6>
        <small class="text-muted">
            <i class="fas fa-sort-amount-down mr-1"></i> Diurutkan berdasarkan task aktif terbanyak
        </small>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" id="capacityTable">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th>Developer</th>
                        <th>Role</th>
                        <th class="text-center">Belum Dikerjakan</th>
                        <th class="text-center">Sedang Dikerjakan</th>
                        <th class="text-center">Done</th>
                        <th class="text-center">Task Aktif</th>
                        <th class="text-center" style="width: 200px;">Beban Kerja</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($developers as $i => $dev)
                        @php
                            $badge = \App\Models\Developer::workloadBadge($dev->active_count);
                            // Lebar bar workload: maks ditampilkan di 100% saat 10+ task
                            $barWidth = min(100, ($dev->active_count / 10) * 100);
                            $barClass = match($dev->workload_status) {
                                'overload' => 'bg-danger',
                                'normal'   => 'bg-warning',
                                default    => 'bg-success',
                            };
                        @endphp
                        <tr class="{{ $dev->workload_status === 'overload' ? 'table-danger' : '' }}">
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $dev->name }}</div>
                                <small class="text-muted">{{ $dev->email }}</small>
                            </td>
                            <td>
                                <span class="badge badge-light border text-uppercase" style="font-size: 10px;">
                                    {{ $dev->role }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $dev->todo_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $dev->in_progress_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success">{{ $dev->done_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold h6 mb-0
                                    {{ $dev->workload_status === 'overload' ? 'text-danger' :
                                       ($dev->workload_status === 'normal' ? 'text-warning' : 'text-success') }}">
                                    {{ $dev->active_count }}
                                </span>
                                <small class="text-muted d-block">task aktif</small>
                            </td>
                            <td>
                                {{-- Progress bar workload --}}
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 mr-2" style="height: 10px; border-radius: 5px;">
                                        <div class="progress-bar {{ $barClass }}"
                                             role="progressbar"
                                             style="width: {{ $barWidth }}%"
                                             aria-valuenow="{{ $dev->active_count }}"
                                             aria-valuemin="0" aria-valuemax="10">
                                        </div>
                                    </div>
                                    <small class="text-muted text-nowrap">{{ $dev->active_count }}/10</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badge['class'] }} px-3 py-2">
                                    <i class="fas {{ $badge['icon'] }} mr-1"></i>
                                    {{ $badge['label'] }}
                                </span>
                                @if($dev->workload_status === 'overload')
                                    <div class="mt-1">
                                        <small class="text-danger font-weight-bold">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Pertimbangkan redistribusi task
                                        </small>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                Belum ada developer terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Keterangan legenda --}}
    <div class="card-footer bg-transparent">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <small class="text-muted font-weight-bold mr-2">Keterangan:</small>
            <span class="badge badge-success px-3 py-1 mr-2">
                <i class="fas fa-check-circle mr-1"></i> Ringan (0–3 task aktif)
            </span>
            <span class="badge badge-warning px-3 py-1 mr-2">
                <i class="fas fa-minus-circle mr-1"></i> Normal (4–6 task aktif)
            </span>
            <span class="badge badge-danger px-3 py-1">
                <i class="fas fa-fire mr-1"></i> Beban Berlebih (7+ tugas aktif)
            </span>
        </div>
    </div>
</div>

@endsection
