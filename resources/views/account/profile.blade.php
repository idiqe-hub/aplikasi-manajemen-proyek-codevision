@extends('layouts.admin')

@section('title', 'Profile')
@section('page_title', 'Profile')

@section('content')
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
  </div>

  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Nama</label>
          <input class="form-control" value="{{ $user->name }}" readonly>
        </div>

        <div class="form-group">
          <label>Email</label>
          <div class="input-group">
            <input class="form-control" value="{{ $user->email }}" readonly>
            @if($user->role === 'admin')
              <div class="input-group-append">
                <a href="{{ route('account.email.edit') }}" class="btn btn-outline-primary" title="Ubah Email">
                  <i class="fas fa-edit"></i>
                </a>
              </div>
            @endif
          </div>
          @if($user->role === 'admin')
            <small class="form-text text-muted">
              <a href="{{ route('account.email.edit') }}">Ubah email</a>
            </small>
          @endif
        </div>

        <div class="form-group">
          <label>Role</label>
          <input class="form-control" value="{{ $user->role }}" readonly>
        </div>

        <div class="d-flex gap-2">
          <a href="{{ route('account.password.edit') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-key mr-1"></i> Ganti Password
          </a>
          @if($user->role === 'admin')
            <a href="{{ route('account.email.edit') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-envelope mr-1"></i> Ubah Email
            </a>
          @endif
          <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
