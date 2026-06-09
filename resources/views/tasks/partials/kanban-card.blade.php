<div class="card mb-3 shadow-sm" data-task-id="{{ $task->id }}" style="cursor: grab; border-left: 4px solid {{ $task->status === 'done' ? '#1cc88a' : ($task->status === 'in_progress' ? '#4e73df' : '#858796') }};">
    <div class="card-body p-3">
        {{-- Judul Task --}}
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.9rem;">
                <a href="/tasks/{{ $task->id }}" class="text-decoration-none text-dark"
                   onclick="event.stopPropagation();">
                    {{ $task->title }}
                </a>
            </h6>
            @if($task->comments_count > 0)
                <span class="badge badge-light border ml-2 flex-shrink-0" title="{{ $task->comments_count }} Komentar">
                    <i class="fas fa-comment fa-xs text-gray-500"></i> {{ $task->comments_count }}
                </span>
            @endif
        </div>

        {{-- Project --}}
        <div class="small text-muted mb-1">
            <i class="fas fa-folder fa-xs mr-1"></i> {{ $task->project?->name ?? 'Tanpa Project' }}
        </div>

        {{-- Developer (hanya Admin yang lihat) --}}
        @if(auth()->user()->role === 'admin')
            <div class="small text-muted mb-1">
                <i class="fas fa-user fa-xs mr-1"></i> {{ $task->developer?->name ?? '—' }}
            </div>
        @endif

        {{-- Deadline --}}
        @if($task->deadline)
            @php $isOverdue = $task->status !== 'done' && $task->deadline < now()->toDateString(); @endphp
            <div class="small mb-2 {{ $isOverdue ? 'text-danger font-weight-bold' : 'text-muted' }}">
                <i class="fas fa-calendar-alt fa-xs mr-1"></i>
                {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                @if($isOverdue) <i class="fas fa-exclamation-triangle fa-xs ml-1"></i> @endif
            </div>
        @endif

        {{-- Progress Bar --}}
        <div class="mt-2">
            <div class="d-flex justify-content-between" style="font-size: 11px;">
                <span class="text-muted">Progres</span>
                <span class="progress-text font-weight-bold">{{ $task->progress }}%</span>
            </div>
            <div class="progress mt-1" style="height: 6px; border-radius: 3px;">
                <div class="progress-bar {{ $task->progress >= 100 ? 'bg-success' : 'bg-primary' }}"
                     role="progressbar"
                     style="width: {{ $task->progress }}%"
                     aria-valuenow="{{ $task->progress }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
</div>
