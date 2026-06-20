@extends('layouts.admin')
@section('title','Ubah Developer')
@section('page_title','Ubah Developer')

@section('content')

{{-- Form utama: edit data developer (termasuk ganti password opsional) --}}
<form action="{{ route('developers.update', $developer) }}" method="POST" class="card shadow mb-4">
    @csrf
    @method('PUT')
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Ubah Data Developer</h6>
    </div>
    <div class="card-body">
        @include('developers.partials.form', ['developer' => $developer])
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Perbarui
        </button>
        <a href="{{ route('developers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
</form>

{{-- Section Reset Password: form terpisah, hanya untuk admin --}}
@if(auth()->user()->role === 'admin' && $developer->user)
<div class="card shadow mb-4 border-left-warning">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-key mr-1"></i> Reset Password Developer
        </h6>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Gunakan form ini untuk mereset password akun login developer secara paksa.
            Password baru wajib diisi minimal 8 karakter.
        </p>
        <form action="{{ route('developers.reset-password', $developer) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="font-weight-bold">Password Baru <span class="text-danger">*</span></label>
                <input type="password" name="password"
                       class="form-control @error('password', 'resetPassword') is-invalid @enderror"
                       placeholder="Minimal 8 karakter" required>
                @error('password', 'resetPassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password baru" required>
            </div>

            <button type="submit" class="btn btn-warning">
                <i class="fas fa-redo mr-1"></i> Reset Password
            </button>
        </form>
    </div>
</div>
@endif

@endsection
