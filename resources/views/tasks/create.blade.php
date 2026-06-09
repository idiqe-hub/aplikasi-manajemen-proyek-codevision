@extends('layouts.admin')
@section('title','Tambah Tugas')
@section('page_title','Tambah Tugas')

@section('content')
<form action="{{ route('tasks.store') }}" method="POST" class="card shadow mb-4">
  @csrf
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Tugas</h6>
  </div>
  <div class="card-body">
    @include('tasks.partials.form', ['task'=>null, 'projects'=>$projects, 'developers'=>$developers])
  </div>
  <div class="card-footer d-flex gap-2">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</form>
@endsection
