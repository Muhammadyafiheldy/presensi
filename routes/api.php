<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InternController;
use App\Http\Controllers\Api\PermitController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LogbookController;

Route::post('/login', [InternController::class, 'login']);

// route for interns
Route::get('/interns', [InternController::class, 'index']);
Route::get('/interns/{id}', [InternController::class, 'show']);
Route::get('/interns/qr/{token}', [InternController::class, 'getByQr']);


// only authenticated users can access these routes
Route::middleware('auth:sanctum')->group(function () {

    // for checking if the user is authenticated
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    // routes for attendance
    Route::post('/attendance/pre-scan', [AttendanceController::class, 'preScan']);
    Route::get('/attendance/today', [AttendanceController::class, 'today']);
    Route::get('/attendance/history', [AttendanceController::class, 'history']);

    // route for update intern profile
    Route::post('/profile/update', [InternController::class, 'updateProfile']);
    Route::post('/profile/change-password', [InternController::class, 'changePassword']);

    // route for permit management
    Route::get('/permits', [PermitController::class, 'index']); // Mengambil riwayat izin
    Route::post('/permits', [PermitController::class, 'store']); // Mengirim pengajuan izin baru

    // route for logbook management
    Route::get('/logbooks', [LogbookController::class, 'index']);
    Route::post('/logbooks', [LogbookController::class, 'store']);
    Route::put('/logbooks/{id}', [LogbookController::class, 'update']); // Gunakan PUT untuk update API dengan file gambar (method spoofing)
    Route::delete('/logbooks/{id}', [LogbookController::class, 'destroy']);
});
