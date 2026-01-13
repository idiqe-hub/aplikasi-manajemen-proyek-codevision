@extends('layouts.admin')
@section('title', 'Tambah Developer')
@section('page_title', 'Tambah Developer')

@section('content')
    <form action="{{ route('developers.store') }}" method="POST" class="card shadow mb-4">
        @csrf
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Developer</h6>
        </div>
        <div class="card-body">
            @include('developers.partials.form', ['developer' => null])
        </div>
        <div class="card-footer d-flex gap-2">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('developers.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
@endsection
