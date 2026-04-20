<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Doctor\AppointmentController;
use App\Http\Controllers\Doctor\BookingController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\Patient\MedicalRecordController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\SuperDoctor\AddAdviceAndSupportAndInfoController;
use App\Http\Controllers\SuperDoctor\ApproveAndRejectController;
use App\Http\Controllers\SuperDoctor\SuperDoctorController;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Route;
use App\Models\DeviceToken;

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

    Route::get('/showCenterInformation', [AddAdviceAndSupportAndInfoController::class, 'showCenterInformation']);
    Route::get('/showPsychologicalSupport', [AddAdviceAndSupportAndInfoController::class, 'showPsychologicalSupport']);

    Route::post('/medical-record', [MedicalRecordController::class, 'storemedicalRecord']);
    Route::get('/doctors-schedules', [BookingController::class, 'getDoctorsWithSchedules']);
});

Route::middleware(['auth:sanctum', 'role:super_doctor'])->group(function () {
    Route::post('/storeAdvices', [AddAdviceAndSupportAndInfoController::class, 'storeAdvices']);
    Route::post('/updateAdvice/{id}', [AddAdviceAndSupportAndInfoController::class, 'updateAdvice']);
    Route::delete('/destroyAdvice/{id}', [AddAdviceAndSupportAndInfoController::class, 'destroyAdvice']);

    Route::post('/storeCenterInformation', [AddAdviceAndSupportAndInfoController::class, 'storeCenterInformation']);
    Route::post('/updateCenterInformation/{id}', [AddAdviceAndSupportAndInfoController::class, 'updateCenterInformation']);

    Route::post('/storePsychologicalSupport', [AddAdviceAndSupportAndInfoController::class, 'storePsychologicalSupport']);
    Route::post('/updatePsychologicalSupport/{id}', [AddAdviceAndSupportAndInfoController::class, 'updatePsychologicalSupport']);
    Route::delete('/destroyPsychologicalSupport/{id}', [AddAdviceAndSupportAndInfoController::class, 'destroyPsychologicalSupport']);


    Route::get('/getPendingUsers', [ApproveAndRejectController::class, 'getPendingUsers']);
    Route::get('/rejected-users', [ApproveAndRejectController::class, 'getRejectedUsers']);
    Route::get('/approved-users', [ApproveAndRejectController::class, 'getApprovedUsers']);
    Route::get('/super-doctors', [ApproveAndRejectController::class, 'getSuperDoctors']);
    Route::post('/approveUser/{id}', [ApproveAndRejectController::class, 'approveUser']);
    Route::post('/rejectUser/{id}', [ApproveAndRejectController::class, 'rejectUser']);

    Route::post('toggleDoctorRole/{id}', [SuperDoctorController::class, 'toggleDoctorRole']);
});


Route::middleware(['auth:sanctum', 'role:patient'])->group(function () {
    Route::post('/book-appointment', [MedicalRecordController::class, 'bookAppointment']);
    Route::post('/getAvailableAppointments', [AppointmentController::class, 'getAvailableAppointments']);
    Route::get('/myAppointments', [MedicalRecordController::class, 'myAppointments']);

});

Route::middleware(['auth:sanctum','approved','role:doctor|super_doctor'])->group(function () {
    Route::post('storeSchedule', [BookingController::class, 'storeSchedule']);
    Route::post('/schedule/{id}', [BookingController::class, 'updateSchedule']);
    Route::delete('/schedule/{id}', [BookingController::class, 'deleteSchedule']);
    Route::get('/available-slots', [BookingController::class, 'getAvailableSlots']);
    Route::get('/doctor/schedules', [BookingController::class, 'getMySchedules']);
    Route::get('/doctor/getAllSchedules', [BookingController::class, 'getAllSchedules']);
    Route::get('getAppointments', [AppointmentController::class, 'getAppointments']);
    Route::get('/doctor/getAllSchedulesFilterDay', [BookingController::class, 'getAllSchedulesFilterDay']);
    Route::post('/doctor/getAllSchedulesMonth', [BookingController::class, 'getAllSchedulesMonth']);
    Route::get('/doctor/getAllSchedulesWeek', [BookingController::class, 'getAllSchedulesWeek']);
});


