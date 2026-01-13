@extends('layouts.admin')

@section('title', 'Ganti Password')
@section('page_title', 'Ganti Password')

@section('content')
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Ganti Password</h6>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('account.password.update') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label>Password Lama</label>
        <input type="password" name="current_password" class="form-control" required>
        @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Password Baru</label>
        <input type="password" name="password" class="form-control" required>
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="form-group">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>

      <button class="btn btn-primary">Simpan</button>
      <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>

    </form>
  </div>
</div>
@endsection

