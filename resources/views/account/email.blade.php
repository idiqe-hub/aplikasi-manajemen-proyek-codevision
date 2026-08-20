@extends('layouts.admin')

@section('title', 'Ubah Email')
@section('page_title', 'Ubah Email')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">

    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
          <i class="fas fa-envelope mr-1"></i> Ubah Email
        </h6>
      </div>

      <div class="card-body">

        {{-- Info email saat ini --}}
        <div class="alert alert-info py-2 mb-4">
          <i class="fas fa-info-circle mr-1"></i>
          Email aktif saat ini: <strong>{{ $user->email }}</strong>
        </div>

        <form id="email-form" action="{{ route('account.email.update') }}" method="POST">
          @csrf
          @method('PUT')

          <div class="form-group">
            <label for="email">Email Baru <span class="text-danger">*</span></label>
            <input
              type="email"
              id="email"
              name="email"
              class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email') }}"
              placeholder="contoh@domain.com"
              required
              autocomplete="email"
            >
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="current_password">Konfirmasi dengan Password <span class="text-danger">*</span></label>
            <input
              type="password"
              id="current_password"
              name="current_password"
              class="form-control @error('current_password') is-invalid @enderror"
              placeholder="Masukkan password Anda"
              required
              autocomplete="current-password"
            >
            @error('current_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
              <i class="fas fa-lock fa-xs mr-1"></i>
              Demi keamanan, masukkan password Anda untuk mengonfirmasi perubahan email.
            </small>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" id="email-btn" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i>
              <span id="email-btn-text">Simpan Email Baru</span>
              <span id="email-btn-spinner" class="spinner-border spinner-border-sm ml-1 d-none" role="status"></span>
            </button>
            <a href="{{ route('account.profile') }}" class="btn btn-secondary">Kembali</a>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    var form    = document.getElementById('email-form');
    var btn     = document.getElementById('email-btn');
    var btnText = document.getElementById('email-btn-text');
    var spinner = document.getElementById('email-btn-spinner');

    if (!form) return;

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btnText.textContent = 'Menyimpan...';
        spinner.classList.remove('d-none');
    });
}());
</script>
@endpush
