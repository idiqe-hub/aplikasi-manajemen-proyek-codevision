@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th>
      <th>Developer</th>
      <th>Tasks Assigned</th>
      <th>Tasks Done</th>
      <th>Tasks On Time</th>
      <th>Est. Hours</th>
      <th>Act. Hours</th>
      <th>KPI Score</th>
      <th>Grade</th>
    </tr>
  </thead>
  <tbody>
    @forelse($developers as $i => $dev)
      @php $kpi = $dev->latestKpi; @endphp
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $dev->name }}</td>
        @if($kpi)
            <td class="text-center">{{ $kpi->tasks_assigned }}</td>
            <td class="text-center">{{ $kpi->tasks_done }}</td>
            <td class="text-center">{{ $kpi->tasks_ontime }}</td>
            <td class="text-center">{{ $kpi->total_estimated_hours }}</td>
            <td class="text-center">{{ $kpi->total_actual_hours }}</td>
            <td class="text-center">{{ number_format($kpi->kpi_score, 1) }}</td>
            <td class="text-center"><strong>{{ $kpi->grade }}</strong></td>
        @else
            <td colspan="7" class="text-center text-muted">Belum ada data KPI.</td>
        @endif
      </tr>
    @empty
      <tr><td colspan="9" class="text-center">Tidak ada data developer.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
