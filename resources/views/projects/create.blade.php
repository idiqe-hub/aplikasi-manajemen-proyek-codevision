@extends('layouts.admin')

@section('title', 'Tambah Project')
@section('page_title','Tambah Project')

@section('content')
<h3 class="mb-3">Tambah Project</h3>

<form action="{{ route('projects.store') }}" method="POST" class="card card-body">
    @csrf
    @include('projects.partials.form', ['project' => null])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</form>
@endsection
