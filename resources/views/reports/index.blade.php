@extends('layouts.admin')

@section('title', 'Laporan')
@section('page_title', 'Laporan')

@section('content')
<div class="container-fluid">

  {{-- Header --}}
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-1 text-gray-800">Laporan</h1>
      <p class="mb-0 text-muted">Pilih laporan, gunakan filter (jika ada), lalu unduh PDF.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
  </div>

  {{-- Quick info --}}
  <div class="alert alert-light border shadow-sm">
    <i class="fas fa-info-circle text-primary"></i>
    Semua laporan dapat diunduh dalam format <strong>PDF</strong>. Beberapa laporan mendukung <strong>filter</strong> untuk mempersempit data.
  </div>

  <div class="row">

    {{-- CARD: 1 Project Aktif --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Report 1</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Proyek Aktif</h5>
              <div class="text-muted small">Status: planned / on_progress</div>
              <span class="badge badge-light border mt-2">Dengan filter</span>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-folder-open fa-2x"></i>
            </div>
          </div>

          <hr class="my-3">

          <div class="d-flex">
            <a href="{{ route('reports.projects_active') }}" class="btn btn-sm btn-primary mr-2">
              <i class="fas fa-eye"></i> Buka
            </a>
            <a href="{{ route('reports.projects_active.pdf') }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 2 Task per Developer --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Report 2</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Tugas per Developer</h5>
              <div class="text-muted small">Filter: developer + range tanggal</div>
              <span class="badge badge-light border mt-2">Dengan filter</span>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-users fa-2x"></i>
            </div>
          </div>

          <hr class="my-3">

          <div class="d-flex">
            <a href="{{ route('reports.tasks_by_developer') }}" class="btn btn-sm btn-info mr-2">
              <i class="fas fa-filter"></i> Buka
            </a>
            {{-- PDF mengikuti filter query jika ada --}}
            <a href="{{ route('reports.tasks_by_developer.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 3 Overdue --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Report 3</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Tugas Terlambat</h5>
              <div class="text-muted small">Deadline lewat & status belum selesai</div>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-exclamation-triangle fa-2x"></i>
            </div>
          </div>

          <hr class="my-3">

          <div class="d-flex">
            <a href="{{ route('reports.tasks_overdue') }}" class="btn btn-sm btn-danger mr-2">
              <i class="fas fa-eye"></i> Buka
            </a>
            <a href="{{ route('reports.tasks_overdue.pdf') }}" class="btn btn-sm btn-dark">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 4 Progress per Project --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Report 4</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Progress per Project</h5>
              <div class="text-muted small">Olahan: AVG progress + COUNT task</div>
              <span class="badge badge-light border mt-2">Dengan filter</span>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-chart-line fa-2x"></i>
            </div>
          </div>

          <hr class="my-3">

          <div class="d-flex">
            <a href="{{ route('reports.project_progress') }}" class="btn btn-sm btn-warning mr-2">
              <i class="fas fa-filter"></i> Buka
            </a>
            <a href="{{ route('reports.project_progress.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 5 Estimasi vs Realisasi --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Report 5</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Estimasi vs Realisasi</h5>
              <div class="text-muted small">Olahan: SUM estimated vs actual hours</div>
              <span class="badge badge-light border mt-2">Dengan filter</span>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-clock fa-2x"></i>
            </div>
          </div>

          <hr class="my-3">

          <div class="d-flex">
            <a href="{{ route('reports.hours_summary') }}" class="btn btn-sm btn-success mr-2">
              <i class="fas fa-filter"></i> Buka
            </a>
            <a href="{{ route('reports.hours_summary.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 6 Beban Kerja Developer --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Report 6</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Beban Kerja Developer</h5>
              <div class="text-muted small">Status beban berdasarkan task aktif</div>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-user-md fa-2x"></i>
            </div>
          </div>
          <hr class="my-3">
          <div class="d-flex">
            <a href="{{ route('reports.workload') }}" class="btn btn-sm btn-primary mr-2">
              <i class="fas fa-eye"></i> Buka
            </a>
            <a href="{{ route('reports.workload.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 7 Tingkat Efisiensi Waktu --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Report 7</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Tingkat Efisiensi Waktu</h5>
              <div class="text-muted small">Estimasi vs Aktual untuk task selesai</div>
              <span class="badge badge-light border mt-2">Dengan filter</span>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-stopwatch fa-2x"></i>
            </div>
          </div>
          <hr class="my-3">
          <div class="d-flex">
            <a href="{{ route('reports.time_efficiency') }}" class="btn btn-sm btn-info mr-2">
              <i class="fas fa-filter"></i> Buka
            </a>
            <a href="{{ route('reports.time_efficiency.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 8 Distribusi Task per Project --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Report 8</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Distribusi Task per Project</h5>
              <div class="text-muted small">Ringkasan status task setiap project</div>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-project-diagram fa-2x"></i>
            </div>
          </div>
          <hr class="my-3">
          <div class="d-flex">
            <a href="{{ route('reports.task_distribution') }}" class="btn btn-sm btn-warning mr-2">
              <i class="fas fa-eye"></i> Buka
            </a>
            <a href="{{ route('reports.task_distribution.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CARD: 9 Laporan KPI Bulanan --}}
    <div class="col-lg-6 mb-4">
      <div class="card shadow h-100 border-left-success">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Report 9</div>
              <h5 class="mb-1 font-weight-bold text-gray-800">Laporan KPI Bulanan</h5>
              <div class="text-muted small">Rekapitulasi Key Performance Indicator developer</div>
            </div>
            <div class="text-gray-300">
              <i class="fas fa-star fa-2x"></i>
            </div>
          </div>
          <hr class="my-3">
          <div class="d-flex">
            <a href="{{ route('kpi.index') }}" class="btn btn-sm btn-success mr-2">
              <i class="fas fa-external-link-alt"></i> Buka Modul KPI
            </a>
            <a href="{{ route('kpi.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
              <i class="fas fa-file-pdf"></i> PDF
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
