@extends('layouts.admin')
@section('title','Data Tugas')
@section('page_title','Data Tugas')

@php
  $statusBadge = fn($s) => $s==='done' ? 'success' : ($s==='in_progress' ? 'warning' : 'secondary');
  $statusLabel = fn($s) => $s==='done' ? 'Selesai' : ($s==='in_progress' ? 'Sedang Dikerjakan' : 'Belum Dikerjakan');
@endphp

@section('content')
<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Data Tugas</h6>
    <div>
      <a href="{{ route('tasks.kanban') }}" class="btn btn-secondary btn-sm mr-1">
        <i class="fas fa-columns"></i> Tampilan Kanban
      </a>
      <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Tugas
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" width="100%">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Judul</th>
            <th>Proyek</th>
            <th>Developer</th>
            <th>Status</th>
            <th>Progres</th>
            <th>Tenggat Waktu</th>
            <th style="width:300px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $t)
            @php
              $isOverdue = $t->deadline && $t->deadline < now()->toDateString() && $t->status !== 'done';
            @endphp
            <tr id="task-row-{{ $t->id }}" class="{{ $isOverdue ? 'table-danger' : '' }}">
              <td>{{ ($tasks->currentPage()-1) * $tasks->perPage() + $loop->iteration }}</td>
              <td class="font-weight-bold">
                {{ $t->title }}
                @if($isOverdue)
                  <span class="badge badge-danger badge-pill ml-1" title="Overdue"><i class="fas fa-exclamation"></i></span>
                @endif
              </td>
              <td>{{ $t->project?->name ?? '-' }}</td>
              <td>{{ $t->developer?->name ?? '-' }}</td>
              <td id="task-status-{{ $t->id }}">
                <span class="badge badge-{{ $statusBadge($t->status) }}">{{ $statusLabel($t->status) }}</span>
              </td>
              <td id="task-progress-{{ $t->id }}">
                <div class="progress" style="height: 16px;">
                  <div class="progress-bar" role="progressbar" style="width: {{ $t->progress }}%;">
                    {{ $t->progress }}%
                  </div>
                </div>
              </td>
              <td class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">{{ $t->deadline ?? '-' }}</td>
              <td>
                <div class="d-flex align-items-center flex-wrap" style="gap:4px;">
                  <a href="{{ route('tasks.show', $t) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> Detail
                  </a>
                  <a href="{{ route('tasks.edit', $t) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Ubah
                  </a>
                  @if($t->status !== 'done')
                    <button class="btn btn-success btn-sm btn-quick-complete"
                            id="qc-btn-{{ $t->id }}"
                            data-task-id="{{ $t->id }}"
                            data-url="{{ route('tasks.quick-complete', $t) }}"
                            title="Tandai Selesai Sekarang">
                      <i class="fas fa-check"></i> Selesai
                    </button>
                  @else
                    <span class="btn btn-sm btn-success disabled">
                      <i class="fas fa-check-circle"></i> Done
                    </span>
                  @endif
                  <form action="{{ route('tasks.destroy', $t) }}" method="POST" class="m-0"
                        onsubmit="return confirm('Yakin hapus tugas ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-4">Data tugas belum tersedia.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $tasks->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
  document.querySelectorAll('.btn-quick-complete').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var taskId = this.dataset.taskId;
      var url    = this.dataset.url;
      var btnEl  = this;

      btnEl.disabled = true;
      btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      fetch(url, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          var statusCell = document.getElementById('task-status-' + taskId);
          if (statusCell) statusCell.innerHTML = '<span class="badge badge-success">Selesai</span>';

          var progressCell = document.getElementById('task-progress-' + taskId);
          if (progressCell) progressCell.innerHTML = '<div class="progress" style="height:16px;"><div class="progress-bar" style="width:100%;">100%</div></div>';

          btnEl.outerHTML = '<span class="btn btn-sm btn-success disabled"><i class="fas fa-check-circle"></i> Done</span>';

          var row = document.getElementById('task-row-' + taskId);
          if (row) row.classList.remove('table-danger');

          if (window.showToast) showToast('success', data.message || 'Task berhasil diselesaikan!');
        } else {
          btnEl.disabled = false;
          btnEl.innerHTML = '<i class="fas fa-check"></i> Selesai';
          if (window.showToast) showToast('error', 'Gagal menandai selesai.');
        }
      })
      .catch(() => {
        btnEl.disabled = false;
        btnEl.innerHTML = '<i class="fas fa-check"></i> Selesai';
        if (window.showToast) showToast('error', 'Terjadi kesalahan jaringan.');
      });
    });
  });
})();
</script>
@endpush
