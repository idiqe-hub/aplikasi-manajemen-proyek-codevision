@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th><th>Nama Project</th><th>Client</th><th>Start</th><th>Deadline</th><th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($projects as $i => $p)
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $p->name }}</td>
        <td>{{ $p->client_name ?? '-' }}</td>
        <td class="text-center">{{ $p->start_date ?? '-' }}</td>
        <td class="text-center">{{ $p->end_date ?? '-' }}</td>
        <td class="text-center">{{ strtoupper($p->status) }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
