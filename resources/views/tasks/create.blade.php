@extends('layouts.admin')
@section('title','Tambah Tugas')
@section('page_title','Tambah Tugas')

@section('content')
<form id="task-create-form" action="{{ route('tasks.store') }}" method="POST" class="card shadow mb-4">
  @csrf
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Tugas</h6>
  </div>
  <div class="card-body">
    @include('tasks.partials.form', ['task'=>null, 'projects'=>$projects, 'developers'=>$developers])
  </div>
  <div class="card-footer d-flex gap-2">
    <button id="task-create-btn" class="btn btn-primary">
      <span id="task-create-text">Simpan</span>
      <span id="task-create-spinner" class="spinner-border spinner-border-sm ml-1 d-none" role="status"></span>
    </button>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    var form    = document.getElementById('task-create-form');
    var btn     = document.getElementById('task-create-btn');
    var btnText = document.getElementById('task-create-text');
    var spinner = document.getElementById('task-create-spinner');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Loading state
        btn.disabled = true;
        btnText.textContent = 'Menyimpan...';
        spinner.classList.remove('d-none');

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        })
        .then(function (res) {
            // Jika validasi error (422), lempar ke catch
            if (!res.ok) return res.json().then(function (d) { throw d; });
            return res.json();
        })
        .then(function (data) {
            if (data.success) {
                // Simpan pesan ke sessionStorage agar tampil di halaman tujuan
                sessionStorage.setItem('flash_toast', JSON.stringify({
                    message : data.message || 'Task berhasil ditambahkan.',
                    type    : 'success'
                }));
                window.location.href = data.redirect || '{{ route('tasks.index') }}';
            }
        })
        .catch(function (err) {
            btn.disabled = false;
            btnText.textContent = 'Simpan';
            spinner.classList.add('d-none');

            if (err && err.errors) {
                // Validasi Laravel — tampilkan pesan pertama
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
