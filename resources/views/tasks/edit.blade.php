@extends('layouts.admin')
@section('title','Ubah Tugas')
@section('page_title','Ubah Tugas')

@section('content')
<form action="{{ route('tasks.update',$task) }}" method="POST" class="card shadow mb-4">
  @csrf
  @method('PUT')
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Ubah Data Tugas</h6>
  </div>
  <div class="card-body">
    @include('tasks.partials.form', ['task'=>$task, 'projects'=>$projects, 'developers'=>$developers])
  </div>
  <div class="card-footer d-flex gap-2">
    <button class="btn btn-primary">Perbarui</button>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</form>
@endsection
