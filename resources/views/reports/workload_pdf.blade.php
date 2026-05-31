@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th>
      <th>Developer</th>
      <th>Task To Do</th>
      <th>Task In Progress</th>
      <th>Total Aktif</th>
      <th>Task Selesai</th>
      <th>Status Beban</th>
    </tr>
  </thead>
  <tbody>
    @forelse($developers as $i => $dev)
      @php
        $aktif = $dev->active_count;
        if ($aktif <= 3) $status = 'Ringan';
        elseif ($aktif <= 6) $status = 'Normal';
        else $status = 'Overload';
      @endphp
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $dev->name }}</td>
        <td class="text-center">{{ $dev->todo_count }}</td>
        <td class="text-center">{{ $dev->in_progress_count }}</td>
        <td class="text-center font-weight-bold">{{ $aktif }}</td>
        <td class="text-center">{{ $dev->done_count }}</td>
        <td class="text-center">{{ $status }}</td>
      </tr>
    @empty
      <tr><td colspan="7" class="text-center">Tidak ada data developer.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
