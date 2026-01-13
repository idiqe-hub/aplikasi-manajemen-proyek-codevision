@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th><th>Project</th><th>Status</th><th>Total Task</th><th>Done</th><th>Avg Progress</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rows as $i => $r)
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $r->name }}</td>
        <td class="text-center">{{ strtoupper($r->status) }}</td>
        <td class="text-center">{{ $r->total_tasks }}</td>
        <td class="text-center">{{ $r->done_tasks }}</td>
        <td class="text-center">{{ number_format($r->avg_progress, 1) }}%</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
