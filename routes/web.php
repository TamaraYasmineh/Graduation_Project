<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\FirebaseController;

// Route::get('/', function () {
//     return "المشروع شغال تمام 🔥";
// });
Route::get('/test-mail', function () {
    Mail::raw('OTP test', function ($message) {
        $message->to('btwl46693@gmail.com')
                ->subject('Test Mail');
    });

    return 'sent';
});
Route::get('/test-otp-mail', function () {
    Mail::to('btwl46693@gmail.com')
        ->queue(new \App\Mail\UserLoginOtp(123456));

    return 'OTP mail queued';
});




//Route::get('/firebase-test', [FirebaseController::class, 'test']);
