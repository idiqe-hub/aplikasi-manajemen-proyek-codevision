@extends('layouts.admin')
@section('title','Detail Tugas')
@section('page_title','Detail Tugas')

@section('content')

<div class="row">
    {{-- Bagian Kiri: Informasi Task & Komentar --}}
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Tugas</h6>
                <div>
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary btn-sm mr-1">
                        <i class="fas fa-edit"></i> Ubah
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

                    <dt class="col-sm-4 text-muted">Proyek</dt>
                    <dd class="col-sm-8">{{ $task->project?->name ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Developer</dt>
                    <dd class="col-sm-8">{{ $task->developer?->name ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $task->status === 'done' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-warning' : 'badge-secondary') }}">
                            {{ $task->status === 'done' ? 'Selesai' : ($task->status === 'in_progress' ? 'Sedang Dikerjakan' : 'Belum Dikerjakan') }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Progres</dt>
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

                    <dt class="col-sm-4 text-muted">Tenggat Waktu</dt>
                    <dd class="col-sm-8">
                        @if($task->deadline)
                            @php $isOverdue = $task->status !== 'done' && $task->deadline < now()->toDateString(); @endphp
                            <span class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                @if($isOverdue)
                                    <i class="fas fa-exclamation-triangle ml-1" title="Terlambat"></i>
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

        {{-- Komentar / Diskusi Task --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-comments mr-1"></i> Diskusi Task
                </h6>
            </div>
            <div class="card-body">
                {{-- Daftar Komentar --}}
                @if($task->comments && $task->comments->count() > 0)
                    <div class="mb-4">
                        @foreach($task->comments as $comment)
                            <div class="mb-3 {{ $loop->last ? '' : 'border-bottom pb-3' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong>
                                        <i class="fas fa-user-circle text-gray-400 mr-1"></i> 
                                        {{ $comment->user->name }}
                                        <span class="badge badge-light border text-uppercase ml-1" style="font-size: 10px;">
                                            {{ $comment->user->role }}
                                        </span>
                                    </strong>
                                    <small class="text-muted" title="{{ $comment->created_at->format('d M Y, H:i:s') }}">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="text-gray-800 ml-4 pl-1" style="white-space: pre-line;">
                                    {{ $comment->comment }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4 mb-3">
                        <i class="far fa-comments fa-2x mb-2 d-block text-gray-300"></i>
                        Belum ada diskusi di task ini.
                    </div>
                @endif

                {{-- Form Tambah Komentar (Kecuali Client) --}}
                @if(auth()->user()->role !== 'client')
                    @php
                        // Developer hanya boleh komentar di task-nya sendiri
                        $canComment = true;
                        if(auth()->user()->role === 'developer') {
                            $dev = auth()->user()->developer;
                            if(!$dev || $task->developer_id !== $dev->id) {
                                $canComment = false;
                            }
                        }
                    @endphp

                    @if($canComment)
                        <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="mt-2 border-top pt-3">
                            @csrf
                            <div class="form-group">
                                <label for="comment" class="sr-only">Tulis Komentar</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Tulis komentar atau diskusi Anda di sini..." required maxlength="1000"></textarea>
                                @error('comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane mr-1"></i> Kirim Komentar
                                </button>
                            </div>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Bagian Kanan: Task Activity Log --}}
    <div class="col-lg-5">
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
