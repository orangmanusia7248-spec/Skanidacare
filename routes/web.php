<?php

use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CohortController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiseaseCategoryController;
use App\Http\Controllers\HospitalInformationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest:admin')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth:admin')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/hospital', [HospitalInformationController::class, 'edit'])->name('hospital.edit');
    Route::put('/hospital', [HospitalInformationController::class, 'update'])->name('hospital.update');
    Route::resource('patients', PatientController::class);
    Route::resource('diseases', DiseaseCategoryController::class)->except(['show'])
        ->parameters(['diseases' => 'disease']);
    Route::get('/cohorts', [CohortController::class, 'index'])->name('cohorts.index');
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('/reports/csv', [ReportController::class, 'csv'])->name('reports.csv');
    Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password');
});
