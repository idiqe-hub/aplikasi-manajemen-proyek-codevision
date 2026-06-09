@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th>
      <th>Developer</th>
      <th>Jumlah Task Selesai</th>
      <th>Rata-rata Waktu Aktual (Jam)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($results as $i => $res)
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $res->developer?->name ?? '-' }}</td>
        <td class="text-center">{{ $res->task_count }}</td>
        <td class="text-center">{{ number_format($res->avg_actual_hours, 2) }}</td>
      </tr>
    @empty
      <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
