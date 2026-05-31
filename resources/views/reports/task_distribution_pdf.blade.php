@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th>
      <th>Project</th>
      <th>To Do</th>
      <th>In Progress</th>
      <th>Done</th>
      <th>Total Task</th>
      <th>Persentase Selesai</th>
    </tr>
  </thead>
  <tbody>
    @forelse($projects as $i => $p)
      @php
        $total = $p->total_count;
        $done = $p->done_count;
        $pct = $total > 0 ? ($done / $total) * 100 : 0;
      @endphp
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $p->name }}</td>
        <td class="text-center">{{ $p->todo_count }}</td>
        <td class="text-center">{{ $p->in_progress_count }}</td>
        <td class="text-center">{{ $done }}</td>
        <td class="text-center font-weight-bold">{{ $total }}</td>
        <td class="text-center">{{ number_format($pct, 1) }}%</td>
      </tr>
    @empty
      <tr><td colspan="7" class="text-center">Tidak ada data project.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
