@extends('layouts.admin')
@section('title','Detail Developer')
@section('page_title','Detail Developer')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Detail Developer</h6>
        <a href="{{ route('developers.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <p><b>Nama:</b> {{ $developer->name }}</p>
        <p><b>Email:</b> {{ $developer->email }}</p>
        <p><b>Peran:</b> {{ $developer->role }}</p>
        <p><b>Skill:</b> {{ $developer->skill ?? '-' }}</p>
    </div>
</div>
@endsection
