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
          <input class="form-control" value="{{ $user->email }}" readonly>
        </div>

        <div class="form-group">
          <label>Role</label>
          <input class="form-control" value="{{ $user->role }}" readonly>
        </div>
              <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>
@endsection
