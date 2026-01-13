@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th><th>Task</th><th>Project</th><th>Developer</th><th>Status</th><th>Deadline</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tasks as $i => $t)
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $t->title }}</td>
        <td>{{ $t->project?->name ?? '-' }}</td>
        <td>{{ $t->developer?->name ?? '-' }}</td>
        <td class="text-center">{{ strtoupper($t->status) }}</td>
        <td class="text-center">{{ $t->deadline }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center">Tidak ada overdue task.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
