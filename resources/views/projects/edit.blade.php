@extends('layouts.admin')

@section('title', 'Edit Project')
@section('page_title','Edit Project')
@section('content')
<h3 class="mb-3">Edit Project</h3>

<form action="{{ route('projects.update', $project) }}" method="POST" class="card card-body">
    @csrf
    @method('PUT')
    @include('projects.partials.form', ['project' => $project])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</form>
@endsection
