@extends('layouts.admin')
@section('title','Ubah Tugas')
@section('page_title','Ubah Tugas')

@section('content')
<form id="task-edit-form" action="{{ route('tasks.update',$task) }}" method="POST" class="card shadow mb-4">
  @csrf
  @method('PUT')
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Ubah Data Tugas</h6>
  </div>
  <div class="card-body">
    @include('tasks.partials.form', ['task'=>$task, 'projects'=>$projects, 'developers'=>$developers])
  </div>
  <div class="card-footer d-flex gap-2">
    <button id="task-edit-btn" class="btn btn-primary">
      <span id="task-edit-text">Perbarui</span>
      <span id="task-edit-spinner" class="spinner-border spinner-border-sm ml-1 d-none" role="status"></span>
    </button>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    var form    = document.getElementById('task-edit-form');
    var btn     = document.getElementById('task-edit-btn');
    var btnText = document.getElementById('task-edit-text');
    var spinner = document.getElementById('task-edit-spinner');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Loading state
        btn.disabled = true;
        btnText.textContent = 'Menyimpan...';
        spinner.classList.remove('d-none');

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST', // FormData dengan @method('PUT') sudah menyertakan _method=PUT
            headers: {
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        })
        .then(function (res) {
            if (!res.ok) return res.json().then(function (d) { throw d; });
            return res.json();
        })
        .then(function (data) {
            if (data.success) {
                window.showToast(data.message || 'Task berhasil diperbarui.', 'success');
                setTimeout(function () {
                    window.location.href = data.redirect || '{{ route('tasks.index') }}';
                }, 900);
            }
        })
        .catch(function (err) {
            btn.disabled = false;
            btnText.textContent = 'Perbarui';
            spinner.classList.add('d-none');

            if (err && err.errors) {
                var messages = Object.values(err.errors).flat();
                window.showToast(messages[0], 'error');
            } else {
                window.showToast('Terjadi kesalahan. Coba lagi.', 'error');
            }
        });
    });
}());
</script>
@endpush
