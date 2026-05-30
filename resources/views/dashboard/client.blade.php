@extends('layouts.admin')
@section('title', 'Dashboard Client')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid">

    {{-- Header Sambutan --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                Selamat Datang, {{ $client?->name ?? auth()->user()->name }}
            </h1>
            <p class="mb-0 text-muted">
                <i class="fas fa-building mr-1"></i>
                {{ $client?->company ?? 'Client Dashboard' }} &mdash; Status project Anda secara real-time.
            </p>
        </div>
        <div>
            <span class="badge badge-primary px-3 py-2">
                <i class="fas fa-shield-alt mr-1"></i> Mode: Read-Only
            </span>
        </div>
    </div>

    @if(!$client)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Akun Anda belum terhubung ke data client. Silakan hubungi administrator.
        </div>
    @else

    {{-- Stat Cards --}}
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Project</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProjects }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Task</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Task Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $doneTasks }}</div>
                            @if($totalTasks > 0)
                                <div class="text-muted small">{{ round(($doneTasks / $totalTasks) * 100) }}% dari total</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Task Overdue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overdueTasks }}</div>
                            <div class="text-muted small">Deadline terlewat</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row stats --}}

    {{-- Daftar Project --}}
    @forelse($projects as $project)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-folder mr-1"></i> {{ $project->name }}
                </h6>
                <small class="text-muted">
                    {{ $project->start_date ?? '-' }} &mdash; {{ $project->end_date ?? '-' }}
                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{-- Badge Status --}}
                <span class="badge px-3 py-2
                    {{ $project->status === 'completed'  ? 'badge-success'  :
                       ($project->status === 'on_progress' ? 'badge-warning' : 'badge-secondary') }}">
                    {{ strtoupper(str_replace('_', ' ', $project->status)) }}
                </span>
                {{-- Badge Overdue --}}
                @if($project->overdue_count > 0)
                    <span class="badge badge-danger px-3 py-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $project->overdue_count }} Overdue
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body">

            {{-- Rata-rata Progress Project --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-xs font-weight-bold text-gray-600">Progress Rata-rata</span>
                    <span class="text-xs font-weight-bold text-gray-800">{{ $project->avg_progress }}%</span>
                </div>
                <div class="progress" style="height: 12px; border-radius: 6px;">
                    <div class="progress-bar
                        {{ $project->avg_progress >= 80 ? 'bg-success' : ($project->avg_progress >= 40 ? 'bg-warning' : 'bg-danger') }}"
                        role="progressbar"
                        style="width: {{ $project->avg_progress }}%"
                        aria-valuenow="{{ $project->avg_progress }}"
                        aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>

            {{-- Ringkasan Task --}}
            <div class="row text-center mb-3">
                <div class="col-3">
                    <div class="border rounded py-2">
                        <div class="h5 mb-0 font-weight-bold text-gray-700">{{ $project->tasks_count }}</div>
                        <div class="text-xs text-muted">Total Task</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border rounded py-2">
                        <div class="h5 mb-0 font-weight-bold text-secondary">{{ $project->todo_count }}</div>
                        <div class="text-xs text-muted">Todo</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border rounded py-2">
                        <div class="h5 mb-0 font-weight-bold text-warning">{{ $project->in_progress_count }}</div>
                        <div class="text-xs text-muted">In Progress</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border rounded py-2">
                        <div class="h5 mb-0 font-weight-bold text-success">{{ $project->done_count }}</div>
                        <div class="text-xs text-muted">Done</div>
                    </div>
                </div>
            </div>

            {{-- Tabel Task (read-only) --}}
            @if($project->tasks_count > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Task</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th class="text-center" style="width: 90px;">Progress</th>
                            <th class="text-center" style="width: 110px;">Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->tasks()->with('developer')->orderBy('status')->get() as $task)
                        <tr>
                            <td>
                                {{ $task->title }}
                                @if($task->developer)
                                    <br><small class="text-muted">
                                        <i class="fas fa-user-circle mr-1"></i>{{ $task->developer->name }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge
                                    {{ $task->status === 'done'        ? 'badge-success'   :
                                       ($task->status === 'in_progress' ? 'badge-warning' : 'badge-secondary') }}">
                                    {{ strtoupper(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="progress progress-sm" style="height:8px;">
                                    <div class="progress-bar
                                        {{ $task->progress >= 80 ? 'bg-success' : ($task->progress >= 40 ? 'bg-warning' : 'bg-danger') }}"
                                        role="progressbar"
                                        style="width: {{ $task->progress }}%">
                                    </div>
                                </div>
                                <small>{{ $task->progress }}%</small>
                            </td>
                            <td class="text-center">
                                @if($task->deadline)
                                    @php $isOverdue = $task->status !== 'done' && $task->deadline < now()->toDateString(); @endphp
                                    <span class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                        @if($isOverdue)
                                            <i class="fas fa-exclamation-circle ml-1"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="text-muted text-center mb-0">
                    <i class="fas fa-inbox mr-1"></i> Belum ada task untuk project ini.
                </p>
            @endif

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}

    @empty
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">Belum ada project yang ditugaskan ke akun Anda.</p>
                <p class="small text-muted">Silakan hubungi administrator untuk informasi lebih lanjut.</p>
            </div>
        </div>
    @endforelse

    @endif{{-- end if $client --}}

</div>
@endsection
