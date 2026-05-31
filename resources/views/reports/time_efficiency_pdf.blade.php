@extends('layouts.pdf')
@section('content')
<table class="report">
  <thead>
    <tr>
      <th>No</th>
      <th>Task</th>
      <th>Developer</th>
      <th>Estimasi (Jam)</th>
      <th>Aktual (Jam)</th>
      <th>Efisiensi (%)</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tasks as $i => $task)
      @php
        $est = (float) $task->estimated_hours;
        $act = (float) $task->actual_hours;
        
        if ($act <= 0) {
            $efficiencyStr = '-';
            $status = 'N/A';
        } else {
            $efficiency = ($est / $act) * 100;
            $efficiencyStr = number_format($efficiency, 2) . '%';
            
            if ($efficiency >= 100) $status = 'Efisien';
            elseif ($efficiency >= 80) $status = 'Cukup Efisien';
            else $status = 'Kurang Efisien';
        }
      @endphp
      <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ $task->title }}</td>
        <td>{{ $task->developer?->name ?? '-' }}</td>
        <td class="text-center">{{ $task->estimated_hours ?? '-' }}</td>
        <td class="text-center">{{ $task->actual_hours ?? '-' }}</td>
        <td class="text-center">{{ $efficiencyStr }}</td>
        <td class="text-center">{{ $status }}</td>
      </tr>
    @empty
      <tr><td colspan="7" class="text-center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
