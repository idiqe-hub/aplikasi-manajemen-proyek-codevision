<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\Task;
use Illuminate\Http\Request;

class DeveloperCapacityController extends Controller
{
    /**
     * Halaman Kapasitas Developer — hanya bisa diakses Admin.
     *
     * Menampilkan semua developer beserta ringkasan workload:
     *   - task todo, in_progress, done
     *   - total aktif (todo + in_progress)
     *   - status beban: Ringan (0-3), Normal (4-6), Overload (7+)
     */
    public function index(Request $request)
    {
        $developers = Developer::withCount([
            // Semua task milik developer
            'tasks as total_tasks',

            // Task per status
            'tasks as todo_count'        => fn($q) => $q->where('status', 'todo'),
            'tasks as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
            'tasks as done_count'        => fn($q) => $q->where('status', 'done'),

            // Task aktif = todo + in_progress
            'tasks as active_count'      => fn($q) => $q->whereIn('status', ['todo', 'in_progress']),
        ])
        ->orderBy('active_count', 'desc') // developer paling sibuk di atas
        ->get()
        ->map(function ($dev) {
            // Hitung status workload berdasarkan aturan bisnis
            $dev->workload_status = match (true) {
                $dev->active_count >= 7 => 'overload',
                $dev->active_count >= 4 => 'normal',
                default                  => 'ringan',
            };
            return $dev;
        });

        // Ringkasan global
        $summary = [
            'total_developer' => $developers->count(),
            'overload'        => $developers->where('workload_status', 'overload')->count(),
            'normal'          => $developers->where('workload_status', 'normal')->count(),
            'ringan'          => $developers->where('workload_status', 'ringan')->count(),
        ];

        return view('developers.capacity', compact('developers', 'summary'));
    }
}
