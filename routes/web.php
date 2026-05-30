<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\DeveloperCapacityController;


Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    // Arahkan sesuai role
    return match (auth()->user()->role) {
        'client' => redirect()->route('client.dashboard'),
        default  => redirect()->route('dashboard'),
    };
})->name('home');


Route::middleware(['auth', 'role:admin,developer'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ADMIN ONLY
    Route::middleware('role:admin')->group(function () {
        Route::resource('projects', ProjectController::class);

        // PENTING: route statis /developers/capacity harus SEBELUM resource
        // agar tidak konflik dengan route developers.show ({developer} = 'capacity')
        Route::get('/developers/capacity', [DeveloperCapacityController::class, 'index'])
             ->name('developers.capacity');
        Route::resource('developers', DeveloperController::class);

        Route::resource('clients', ClientController::class);
        Route::put('/developers/{developer}/reset-password', [\App\Http\Controllers\DeveloperController::class, 'resetPassword'])
            ->name('developers.reset-password');

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/projects-active', [ReportController::class, 'projectsActive'])->name('projects_active');
            Route::get('/projects-active/pdf', [ReportController::class, 'pdfProjectsActive'])->name('projects_active.pdf');
            Route::get('/tasks-by-developer', [ReportController::class, 'tasksByDeveloper'])->name('tasks_by_developer');
            Route::get('/tasks-by-developer/pdf', [ReportController::class, 'pdfTasksByDeveloper'])->name('tasks_by_developer.pdf');
            Route::get('/tasks-overdue', [ReportController::class, 'tasksOverdue'])->name('tasks_overdue');
            Route::get('/tasks-overdue/pdf', [ReportController::class, 'pdfTasksOverdue'])->name('tasks_overdue.pdf');
            Route::get('/project-progress', [ReportController::class, 'projectProgress'])->name('project_progress');
            Route::get('/project-progress/pdf', [ReportController::class, 'pdfProjectProgress'])->name('project_progress.pdf');
            Route::get('/hours-summary', [ReportController::class, 'hoursSummary'])->name('hours_summary');
            Route::get('/hours-summary/pdf', [ReportController::class, 'pdfHoursSummary'])->name('hours_summary.pdf');
        });
    });

    Route::middleware('role:admin,developer')->group(function () {
        Route::resource('tasks', TaskController::class);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');

    Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
});

require __DIR__ . '/auth.php';


// ================================================================
// Route khusus CLIENT — read-only, tidak bisa akses menu admin
// ================================================================
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])
         ->name('client.dashboard');
});
