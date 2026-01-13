@extends('layouts.pdf')
@section('content')
@php
  $totalEstimated = (int)($totals->total_estimated ?? 0);
  $totalActual    = (int)($totals->total_actual ?? 0);
  $diff = $totalActual - $totalEstimated;
@endphp

<p><strong>Total Estimasi:</strong> {{ $totalEstimated }} jam</p>
<p><strong>Total Realisasi:</strong> {{ $totalActual }} jam</p>
<p><strong>Selisih:</strong> {{ $diff }} jam</p>

<table class="report">
  <thead>
    <tr>
      <th>No</th><th>Task</th><th>Project</th><th>Estimasi</th><th>Realisasi</th><th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tasks as $i => $t)
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $t->title }}</td>
        <td>{{ $t->project?->name ?? '-' }}</td>
        <td class="text-center">{{ $t->estimated_hours ?? 0 }}</td>
        <td class="text-center">{{ $t->actual_hours ?? 0 }}</td>
        <td class="text-center">{{ strtoupper($t->status) }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
