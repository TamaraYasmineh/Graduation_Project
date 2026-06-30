<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\ConsultationPaymentController;
use App\Http\Controllers\Doctor\AppointmentController;
use App\Http\Controllers\Doctor\BookingController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\MedicalTestController;
use App\Http\Controllers\Patient\DoctorReviewController;
use App\Http\Controllers\Patient\MedicalRecordController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProtocolController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SuperDoctor\AddAdviceAndSupportAndInfoController;
use App\Http\Controllers\SuperDoctor\ApproveAndRejectController;
use App\Http\Controllers\SuperDoctor\EmployeeController;
use App\Http\Controllers\SuperDoctor\SuperDoctorController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']); //
Route::post('/login', [AuthController::class, 'login']); //
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']); //
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
Route::get('medical-records/scan', [MedicalRecordController::class, 'scan'])
    ->name('medical-records.scan');
// Auth
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/patient/profile', [PatientController::class, 'updateProfile']); //
    Route::post('/showProfile', [PatientController::class, 'showProfile']); //
    Route::get('/getDoctorReviews/{doctorId}', [DoctorReviewController::class, 'getDoctorReviews']);
});

// super_doctor|patient|secretary
Route::middleware(['auth:sanctum', 'role:super_doctor|patient|secretary'])->group(function () {

    // Route::get('/getDoctors', [SuperDoctorController::class, 'getDoctors']);

    Route::get('/getDoctorsWithSpecialization', [SuperDoctorController::class, 'getDoctorsWithSpecialization']); //
    Route::get('/getAdvicesForPatientsAndSuper', [AddAdviceAndSupportAndInfoController::class, 'getAdvicesForPatientsAndSuper']); //

    Route::get('/showCenterInformation', [AddAdviceAndSupportAndInfoController::class, 'showCenterInformation']); //
    Route::get('/showPsychologicalSupport', [AddAdviceAndSupportAndInfoController::class, 'showPsychologicalSupport']); //

    Route::post('/medical-record', [MedicalRecordController::class, 'storemedicalRecord']); //
    Route::get('/doctors-schedules', [BookingController::class, 'getDoctorsWithSchedules']);
});

// super_doctor
Route::middleware(['auth:sanctum', 'role:super_doctor'])->group(function () {

    Route::post('/storeAdvices', [AddAdviceAndSupportAndInfoController::class, 'storeAdvices']); //
    Route::post('/updateAdvice/{id}', [AddAdviceAndSupportAndInfoController::class, 'updateAdvice']); //
    Route::delete('/destroyAdvice/{id}', [AddAdviceAndSupportAndInfoController::class, 'destroyAdvice']); //

    Route::post('/storeCenterInformation', [AddAdviceAndSupportAndInfoController::class, 'storeCenterInformation']); //
    Route::post('/updateCenterInformation/{id}', [AddAdviceAndSupportAndInfoController::class, 'updateCenterInformation']); //

    Route::post('/storePsychologicalSupport', [AddAdviceAndSupportAndInfoController::class, 'storePsychologicalSupport']); //
    Route::post('/updatePsychologicalSupport/{id}', [AddAdviceAndSupportAndInfoController::class, 'updatePsychologicalSupport']); //
    Route::delete('/destroyPsychologicalSupport/{id}', [AddAdviceAndSupportAndInfoController::class, 'destroyPsychologicalSupport']); //

    Route::get('/getPendingUsers', [ApproveAndRejectController::class, 'getPendingUsers']); //
    Route::get('/rejected-users', [ApproveAndRejectController::class, 'getRejectedUsers']); //
    Route::get('/approved-users', [ApproveAndRejectController::class, 'getApprovedUsers']); //
    Route::get('/super-doctors', [ApproveAndRejectController::class, 'getSuperDoctors']); //
    Route::post('/approveUser/{id}', [ApproveAndRejectController::class, 'approveUser']); //
    Route::post('/rejectUser/{id}', [ApproveAndRejectController::class, 'rejectUser']); //

    Route::post('toggleDoctorRole/{id}', [SuperDoctorController::class, 'toggleDoctorRole']); //

    Route::patch('/users/{id}/activate', [ApproveAndRejectController::class, 'activateUser']);
    Route::patch('/users/{id}/deactivate', [ApproveAndRejectController::class, 'deactivateUser']);
    Route::get('/payment/dashboard', [PaymentController::class, 'dashboard']);
    Route::get('/PaymentStatistics', [PaymentController::class, 'PaymentStatistics']);

    Route::post('/storeEmployee', [EmployeeController::class, 'storeEmployee']);
    Route::get('/getAllEmployees', [EmployeeController::class, 'getAllEmployees']);
    Route::get('/getEmployeeById/{id}', [EmployeeController::class, 'getEmployeeById']);
    Route::post('/updateEmployeeInfo/{employee}', [EmployeeController::class, 'updateEmployeeInfo']);
    Route::delete('/deleteEmployee/{employee}', [EmployeeController::class, 'deleteEmployee']);

    Route::post('/consultants/internal', [ConsultantController::class, 'addInternalDoctor']);

    Route::post('/consultants/external', [ConsultantController::class, 'addExternalDoctor']);
    Route::post('/update/{consultantId}', [ConsultantController::class, 'update']);
    Route::patch('/consultants/toggle-status/{consultantId}', [ConsultantController::class, 'toggleStatus']);
    Route::get('/consultants/filter', [ConsultantController::class, 'filter']);
});

// patient
Route::middleware(['auth:sanctum', 'role:patient'])->group(function () {

    Route::post('/book-appointment', [MedicalRecordController::class, 'bookAppointment']); //
    Route::post('/getAvailableAppointments', [AppointmentController::class, 'getAvailableAppointments']); //
    Route::get('/myAppointments', [MedicalRecordController::class, 'myAppointments']); //
    Route::get('/payment/status/{paymentId}', [PaymentController::class, 'getPaymentStatus']); //

    Route::post('/addReview', [DoctorReviewController::class, 'addReview']);
    Route::get('medical-record/qr', [MedicalRecordController::class, 'showWithQr'])->name('medical-records.show-qr');

    Route::post('/uploadMedicalTest', [MedicalTestController::class, 'uploadMedicalTest']);
    Route::post(
        '/consultation-payment/create',
        [ConsultationPaymentController::class, 'create']
    );

    Route::get(
        '/consultation-payment/whatsapp/{consultationId}',
        [ConsultationPaymentController::class, 'whatsapp']
    );

    Route::get(
        '/consultation-payment/status/{paymentId}',
        [ConsultationPaymentController::class, 'getPaymentStatus']
    );

    Route::post(
        '/consultation-payment/cancel/{paymentId}',
        [ConsultationPaymentController::class, 'cancel']
    );
    Route::get('/consultants', [ConsultantController::class, 'index']);
});

// patient|super_doctor
Route::middleware(['auth:sanctum', 'role:patient|super_doctor'])->group(function () {

    Route::post('/updateReview/{id}', [DoctorReviewController::class, 'updateReview']);
    Route::delete('/deleteReview/{id}', [DoctorReviewController::class, 'deleteReview']);

    Route::get('/getAllConsultantsForSD', [ConsultantController::class, 'getAllConsultantsForSD']);
});

// doctor|super_doctor
Route::middleware(['auth:sanctum', 'approved', 'role:doctor|super_doctor|secretary'])->group(function () {

    Route::post('storeSchedule', [BookingController::class, 'storeSchedule']); //
    Route::post('/schedule/{id}', [BookingController::class, 'updateSchedule']);
    Route::delete('/schedule/{id}', [BookingController::class, 'deleteSchedule']);
    Route::get('/available-slots', [BookingController::class, 'getAvailableSlots']);
    Route::get('/doctor/schedules', [BookingController::class, 'getMySchedules']);
    Route::get('/doctor/getAllSchedules', [BookingController::class, 'getAllSchedules']);
    Route::get('getAppointments', [AppointmentController::class, 'getAppointments']);
    Route::get('/doctor/getAllSchedulesFilterDay', [BookingController::class, 'getAllSchedulesFilterDay']);
    Route::post('/doctor/getAllSchedulesMonth', [BookingController::class, 'getAllSchedulesMonth']);
    Route::get('/doctor/getAllSchedulesWeek', [BookingController::class, 'getAllSchedulesWeek']);

    Route::get('/getPatientMedicalTests/{id}', [MedicalTestController::class, 'getPatientMedicalTests']);

    Route::get('/getByRecord/{id}', [MedicalTestController::class, 'getByRecord']);
    Route::post('/getPatient', [PatientController::class, 'getPatient']);
    Route::get(
        '/patients/{id}',
        [PatientController::class, 'showPatient']
    );
    Route::apiResource('protocols', ProtocolController::class);
    Route::post('/protocols/{id}', [ProtocolController::class, 'update']);
    Route::apiResource('drugs', DrugController::class);
    Route::post('/drugs/{id}', [DrugController::class, 'update']);
    Route::get('/showAllProtocolwithDrugs', [ProtocolController::class, 'showAllProtocolwithDrugs']);
    Route::get('/showProtocolwithDrugs/{id}', [ProtocolController::class, 'showProtocolwithDrugs']);
    Route::post('/calculate-bsa', [SessionController::class, 'calculateBsa']);
    Route::post('/treatment-plans', [SessionController::class, 'storeTretmentPlane']);
    Route::post('/treatment-plans/{id}', [SessionController::class, 'updateTretmentPlane']);
    Route::post('/treatment-sessions', [SessionController::class, 'storeTreatmentSession']);
    Route::post('/treatment-sessions/{id}', [SessionController::class, 'updateTreatmentSession']);
    Route::get('/treatment-plans', [SessionController::class, 'getAllTreatmentPlans']);
    Route::get('/treatment-plans/{id}', [SessionController::class, 'getTreatmentPlan']);
    Route::delete('/treatment-plans/{id}', [SessionController::class, 'deleteTreatmentPlan']);
    Route::get('/treatment-sessions', [SessionController::class, 'getAllTreatmentSessions']);
    Route::get('/treatment-sessions/{id}', [SessionController::class, 'getTreatmentSession']);
    Route::delete('/treatment-sessions/{id}', [SessionController::class, 'deleteTreatmentSession']);
    Route::get('/patients/{patient}/full-profile', [PatientController::class, 'fullProfile']);
});

// patient|secretary
Route::middleware('auth:sanctum', 'approved', 'role:patient|secretary')->group(function () {
    Route::delete('/deleteMedicalTest/{id}', [MedicalTestController::class, 'deleteMedicalTest']);
});

// secretary
Route::middleware('auth:sanctum', 'approved', 'role:secretary')->group(function () {

    Route::get('/appointments/grouped/{id}', [AppointmentController::class, 'getGroupedAppointments']);
    Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    Route::post('/Secretary/createPatientBySecretary', [AuthController::class, 'createPatientBySecretary']);
    Route::post('/medical-recordBysecretary', [MedicalRecordController::class, 'storeMedicalRecordBySecretary']);
    Route::post(
        '/book-appointment-by-secretary',
        [MedicalRecordController::class, 'bookAppointmentBySecretary']
    );
    Route::post('/uploadTestBySecretary/{record}', [MedicalTestController::class, 'uploadTestBySecretary']);
});

Route::post('/pay', [PaymentController::class, 'create']);

Route::get('/payment/callback/{order}', [PaymentController::class, 'callback'])
    ->name('payment.callback');

Route::get('/payment/trigger/{order}', [PaymentController::class, 'trigger'])
    ->name('payment.trigger');

Route::post('/payment/cancel/{paymentId}', [PaymentController::class, 'cancel']);
// Route::get('/medical-record/qr-test', function () {
//     return response()->json([
//         'status' => true,
//         'message' => 'working'
//     ]);
// });

Route::get(
    '/consultation-payment/callback/{orderId}',
    [ConsultationPaymentController::class, 'callback']
);

Route::get(
    '/consultation-payment/trigger/{orderId}',
    [ConsultationPaymentController::class, 'trigger']
);
