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
    Route::get('/showPsychologicalSupport', [AddAdviceAndSupportAndInfoController::class, 'showPsychologicalSupport']);
});

Route::middleware(['auth:sanctum', 'role:super_doctor'])->group(function () {
    Route::post('/storeAdvices', [AddAdviceAndSupportAndInfoController::class, 'storeAdvices']);
    Route::delete('/destroyAdvice/{id}', [AddAdviceAndSupportAndInfoController::class, 'destroyAdvice']);
    Route::post('/storeCenterInformation', [AddAdviceAndSupportAndInfoController::class, 'storeCenterInformation']);
    Route::post('/updateCenterInformation/{id}', [AddAdviceAndSupportAndInfoController::class, 'updateCenterInformation']);
    Route::post('/storePsychologicalSupport', [AddAdviceAndSupportAndInfoController::class, 'storePsychologicalSupport']);
    Route::post('/updatePsychologicalSupport/{id}', [AddAdviceAndSupportAndInfoController::class, 'updatePsychologicalSupport']);
    Route::delete('/destroyPsychologicalSupport/{id}', [AddAdviceAndSupportAndInfoController::class, 'destroyPsychologicalSupport']);
});

Route::middleware(['auth:sanctum', 'role:patient'])->group(function () {});
