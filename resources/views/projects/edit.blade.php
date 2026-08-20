@extends('layouts.admin')

@section('title', 'Ubah Proyek')
@section('page_title','Ubah Proyek')

@section('content')
<h3 class="mb-3">Ubah Proyek</h3>

<form id="project-edit-form" action="{{ route('projects.update', $project) }}" method="POST" class="card card-body">
    @csrf
    @method('PUT')
    @include('projects.partials.form', ['project' => $project])
    <div class="d-flex gap-2">
        <button id="project-edit-btn" class="btn btn-primary">
            <span id="project-edit-text">Perbarui</span>
            <span id="project-edit-spinner" class="spinner-border spinner-border-sm ml-1 d-none" role="status"></span>
        </button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    var form    = document.getElementById('project-edit-form');
    var btn     = document.getElementById('project-edit-btn');
    var btnText = document.getElementById('project-edit-text');
    var spinner = document.getElementById('project-edit-spinner');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        btn.disabled = true;
        btnText.textContent = 'Menyimpan...';
        spinner.classList.remove('d-none');

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST', // FormData sudah menyertakan _method=PUT
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
                window.showToast(data.message || 'Project berhasil diupdate.', 'success');
                setTimeout(function () {
                    window.location.href = data.redirect || '{{ route('projects.index') }}';
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
