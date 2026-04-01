<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PermitController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LogbookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirect awal
Route::get('/', function () {
    return redirect()->route('admin.interns.index');
});

// --- RUTE GUEST (Hanya bisa diakses jika BELUM login) ---
Route::middleware('guest')->group(function () {
    // Memanggil fungsi dari AdminController
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.process');
});

// --- RUTE PRIVATE ADMIN (Wajib Login) ---
Route::middleware(['auth'])->group(function () {

    // Rute Logout
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Grup untuk URL yang diawali /admin
    Route::prefix('admin')->name('admin.')->group(function () {

        // Modul Peserta
        Route::resource('interns', AdminController::class);
        Route::get('interns/{intern}/id-card', [AdminController::class, 'idCard'])->name('interns.id_card');

        // Modul Izin
        Route::get('permits', [PermitController::class, 'index'])->name('permits.index');
        Route::patch('permits/{permit}/status', [PermitController::class, 'updateStatus'])->name('permits.update_status');

        // Modul Absensi & Scanner
        Route::get('attendance/scan', [AttendanceController::class, 'scanPage'])->name('attendance.scan');
        Route::post('attendance/process', [AttendanceController::class, 'processScan'])->name('attendance.process');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

        // Rute untuk melihat rekapitulasi absensi bulanan semua peserta
        Route::get('/attendance/recap', [AttendanceController::class, 'recap'])->name('attendance.recap');

        // Rute untuk riwayat per 1 user (jika belum ditambahkan sebelumnya)
        Route::get('/interns/{id}/attendance-history', [AttendanceController::class, 'history'])->name('attendance.history');

        Route::get('interns/{id}/logbooks', [LogbookController::class, 'logbooks'])->name('interns.logbooks');
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');
});
