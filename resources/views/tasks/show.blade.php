@extends('layouts.admin')
@section('title','Detail Task')
@section('page_title','Detail Task')

@section('content')

<div class="row">
    {{-- Bagian Kiri: Informasi Task --}}
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Task</h6>
                <div>
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary btn-sm mr-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Judul</dt>
                    <dd class="col-sm-8 font-weight-bold">{{ $task->title }}</dd>

                    <dt class="col-sm-4 text-muted">Project</dt>
                    <dd class="col-sm-8">{{ $task->project?->name ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Developer</dt>
                    <dd class="col-sm-8">{{ $task->developer?->name ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $task->status === 'done' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-warning' : 'badge-secondary') }}">
                            {{ strtoupper(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Progress</dt>
                    <dd class="col-sm-8">
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 mr-2" style="height: 10px;">
                                <div class="progress-bar {{ $task->progress >= 100 ? 'bg-success' : 'bg-primary' }}"
                                     role="progressbar" style="width: {{ $task->progress }}%"
                                     aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <span>{{ $task->progress }}%</span>
                        </div>
                    </dd>

                    <dt class="col-sm-4 text-muted">Deadline</dt>
                    <dd class="col-sm-8">
                        @if($task->deadline)
                            @php $isOverdue = $task->status !== 'done' && $task->deadline < now()->toDateString(); @endphp
                            <span class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                @if($isOverdue)
                                    <i class="fas fa-exclamation-triangle ml-1" title="Overdue"></i>
                                @endif
                            </span>
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="col-sm-4 text-muted">Estimasi Jam</dt>
                    <dd class="col-sm-8">{{ $task->estimated_hours ? $task->estimated_hours . ' Jam' : '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Realisasi Jam</dt>
                    <dd class="col-sm-8">{{ $task->actual_hours ? $task->actual_hours . ' Jam' : '-' }}</dd>

                    <dt class="col-sm-4 text-muted mt-3">Deskripsi</dt>
                    <dd class="col-sm-8 mt-3">
                        {!! nl2br(e($task->description ?: 'Tidak ada deskripsi.')) !!}
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Bagian Kanan: Task Activity Log --}}
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-1"></i> Riwayat Aktivitas
                </h6>
            </div>
            <div class="card-body">
                @if($task->activityLogs->count() > 0)
                    <div class="timeline" style="border-left: 2px solid #e3e6f0; padding-left: 20px; position: relative;">
                        @foreach($task->activityLogs as $log)
                            <div class="mb-4 position-relative">
                                {{-- Icon titik --}}
                                <div style="position: absolute; left: -26px; top: 0; background: white; border: 2px solid #4e73df; border-radius: 50%; width: 12px; height: 12px;"></div>

                                <div class="small text-muted mb-1">
                                    <i class="fas fa-clock mr-1"></i> {{ $log->created_at->format('d M Y, H:i') }}
                                    @if($log->user)
                                        oleh <strong>{{ $log->user->name }}</strong>
                                    @endif
                                </div>
                                <div class="card bg-light border-0">
                                    <div class="card-body py-2 px-3">
                                        {{-- Perubahan Status --}}
                                        @if($log->old_status !== $log->new_status)
                                            <div class="mb-1">
                                                Status diubah dari 
                                                <span class="badge badge-secondary">{{ $log->old_status }}</span> 
                                                <i class="fas fa-arrow-right mx-1 text-muted" style="font-size: 10px;"></i>
                                                <span class="badge {{ $log->new_status === 'done' ? 'badge-success' : 'badge-primary' }}">{{ $log->new_status }}</span>
                                            </div>
                                        @endif
                                        
                                        {{-- Perubahan Progress --}}
                                        @if($log->old_progress != $log->new_progress)
                                            <div class="mb-1">
                                                Progress diperbarui dari 
                                                <span class="font-weight-bold text-gray-700">{{ $log->old_progress }}%</span> 
                                                <i class="fas fa-arrow-right mx-1 text-muted" style="font-size: 10px;"></i>
                                                <span class="font-weight-bold text-primary">{{ $log->new_progress }}%</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comment-slash fa-2x mb-3 d-block text-gray-300"></i>
                        Belum ada riwayat aktivitas.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
