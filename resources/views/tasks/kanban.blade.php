@extends('layouts.admin')
@section('title', 'Kanban Board')
@section('page_title', 'Kanban Board')

@section('content')

{{-- Header Kustom dengan Tombol Switch --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kanban Board</h1>
    <div>
        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-list fa-sm text-white-50 mr-1"></i> Tampilan Tabel
        </a>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary shadow-sm ml-2">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Task
            </a>
        @endif
    </div>
</div>

<div class="row" id="kanban-board">

    {{-- KOLOM TODO --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-secondary">
                <h6 class="m-0 font-weight-bold text-white text-uppercase">
                    <i class="fas fa-clipboard-list mr-1"></i> To Do
                    <span class="badge badge-light float-right" id="count-todo">{{ $kanban['todo']->count() }}</span>
                </h6>
            </div>
            <div class="card-body bg-light rounded-bottom kanban-column p-2"
                 id="col-todo"
                 data-status="todo"
                 style="min-height: 300px;">
                @foreach($kanban['todo'] as $task)
                    @include('tasks.partials.kanban-card', ['task' => $task])
                @endforeach
            </div>
        </div>
    </div>

    {{-- KOLOM IN PROGRESS --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white text-uppercase">
                    <i class="fas fa-cog mr-1"></i> In Progress
                    <span class="badge badge-light float-right" id="count-in_progress">{{ $kanban['in_progress']->count() }}</span>
                </h6>
            </div>
            <div class="card-body bg-light rounded-bottom kanban-column p-2"
                 id="col-in_progress"
                 data-status="in_progress"
                 style="min-height: 300px;">
                @foreach($kanban['in_progress'] as $task)
                    @include('tasks.partials.kanban-card', ['task' => $task])
                @endforeach
            </div>
        </div>
    </div>

    {{-- KOLOM DONE --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-success">
                <h6 class="m-0 font-weight-bold text-white text-uppercase">
                    <i class="fas fa-check-circle mr-1"></i> Done
                    <span class="badge badge-light float-right" id="count-done">{{ $kanban['done']->count() }}</span>
                </h6>
            </div>
            <div class="card-body bg-light rounded-bottom kanban-column p-2"
                 id="col-done"
                 data-status="done"
                 style="min-height: 300px;">
                @foreach($kanban['done'] as $task)
                    @include('tasks.partials.kanban-card', ['task' => $task])
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
{{-- SortableJS dimuat SETELAH SB Admin 2, di luar DOMContentLoaded karena defer --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Inisialisasi SortableJS pada setiap kolom kanban
    var columns = document.querySelectorAll('.kanban-column');

    columns.forEach(function (col) {
        new Sortable(col, {
            group: 'kanban',         // Nama grup agar bisa antar kolom
            animation: 200,
            ghostClass: 'opacity-50',
            dragClass: 'shadow',
            onEnd: function (evt) {
                var itemEl   = evt.item;                          // Card yang di-drag
                var taskId   = itemEl.getAttribute('data-task-id');
                var newStatus = evt.to.getAttribute('data-status');
                var oldStatus = evt.from.getAttribute('data-status');

                if (newStatus === oldStatus) return; // Tidak berubah, abaikan

                updateTaskStatus(taskId, newStatus, oldStatus, itemEl, evt);
            }
        });
    });

    function updateTaskStatus(taskId, newStatus, oldStatus, element, evt) {
        var url = '/tasks/' + taskId + '/kanban-status';

        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            if (data.success) {
                // Perbarui progress bar di card jika pindah ke Done
                if (newStatus === 'done') {
                    var progressBar  = element.querySelector('.progress-bar');
                    var progressText = element.querySelector('.progress-text');
                    if (progressBar) {
                        progressBar.style.width = '100%';
                        progressBar.classList.remove('bg-primary', 'bg-warning');
                        progressBar.classList.add('bg-success');
                        progressBar.setAttribute('aria-valuenow', 100);
                    }
                    if (progressText) {
                        progressText.textContent = '100%';
                    }
                }

                // Update badge counter jumlah task per kolom
                updateCounters();

                console.log('[Kanban] Task ' + taskId + ' moved from ' + oldStatus + ' to ' + newStatus);
            } else {
                // Kembalikan card ke posisi semula
                revertCard(element, oldStatus, evt);
                alert('Gagal memindahkan task. Silakan refresh halaman.');
            }
        })
        .catch(function (error) {
            console.error('[Kanban] Fetch error:', error);
            revertCard(element, oldStatus, evt);
            alert('Terjadi kesalahan jaringan. Task dikembalikan ke posisi semula.');
        });
    }

    function revertCard(element, oldStatus, evt) {
        var fromColumn = document.getElementById('col-' + oldStatus);
        if (fromColumn && evt.oldIndex !== undefined) {
            // Kembalikan element ke kolom asal pada posisi semula
            var refNode = fromColumn.children[evt.oldIndex] || null;
            fromColumn.insertBefore(element, refNode);
        }
        updateCounters();
    }

    function updateCounters() {
        document.getElementById('count-todo').textContent       = document.getElementById('col-todo').children.length;
        document.getElementById('count-in_progress').textContent = document.getElementById('col-in_progress').children.length;
        document.getElementById('count-done').textContent       = document.getElementById('col-done').children.length;
    }
}());
</script>
@endpush
