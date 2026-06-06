<?php

use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\MedicalTestController;
use App\Http\Controllers\Patient\MedicalRecordController;
use App\Models\MedicalTest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return "المشروع شغال تمام 🔥";
});
// Route::get('/test-mail', function () {
//     Mail::raw('OTP test', function ($message) {
//         $message->to('btwl46693@gmail.com')
//                 ->subject('Test Mail');
//     });

//     return 'sent';
// });
// Route::get('/test-otp-mail', function () {
//     Mail::to('btwl46693@gmail.com')
//         ->queue(new \App\Mail\UserLoginOtp(123456));

//     return 'OTP mail queued';
// });



Route::get('/scan', [MedicalRecordController::class, 'scanWeb'])
     ->name('medical-records.scan-web');


// Route::get('/download-medical-test/{id}', [MedicalTestController::class, 'downloadMedicalTest'])
//     ->name('medical-test.download');
Route::get(
    '/download-medical-test/{id}',
    [MedicalTestController::class, 'downloadMedicalTest']
)->name('medical-test.download');

Route::get(
    '/view-medical-test/{id}',
    [MedicalTestController::class, 'viewMedicalTest']
)->name('medical-test.view');
//Route::get('/firebase-test', [FirebaseController::class, 'test']);
