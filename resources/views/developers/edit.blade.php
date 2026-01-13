@extends('layouts.admin')
@section('title','Edit Developer')
@section('page_title','Edit Developer')

@section('content')
<form action="{{ route('developers.update',$developer) }}" method="POST" class="card shadow mb-4">
    @csrf
    @method('PUT')
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Developer</h6>
    </div>
    <div class="card-body">
        @include('developers.partials.form', ['developer' => $developer])
    </div>
    <div class="card-footer d-flex gap-2">
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('developers.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</form>
@endsection
