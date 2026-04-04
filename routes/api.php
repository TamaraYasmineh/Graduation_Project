<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\SuperDoctor\AddAdviceAndSupportAndInfoController;
use App\Http\Controllers\SuperDoctor\SuperDoctorController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/patient/profile', [PatientController::class, 'updateProfile']);
});
Route::middleware(['auth:sanctum', 'role:super_doctor|patient'])->group(function () {
    Route::get('/getDoctors', [SuperDoctorController::class, 'getDoctors']);
    Route::get('/getDoctorsWithSpecialization', [SuperDoctorController::class, 'getDoctorsWithSpecialization']);
    Route::get('/getAdvicesForPatientsAndSuper', [AddAdviceAndSupportAndInfoController::class, 'getAdvicesForPatientsAndSuper']);
});

Route::middleware(['auth:sanctum', 'role:super_doctor'])->group(function () {
    Route::post('/storeAdvices', [AddAdviceAndSupportAndInfoController::class, 'storeAdvices']);
});

Route::middleware(['auth:sanctum', 'role:patient'])->group(function () {});
