<?php

use Illuminate\Support\Facades\Route;
use Lluminate\SmsVerification\Http\Controllers\SmsVerificationController;

Route::middleware(['auth'])->group(function () {
    Route::get('/sms/create', [SmsVerificationController::class, 'add'])
        ->name('sms.create');

    Route::post('/sms/new-sms-verify', [SmsVerificationController::class, 'create'])
        ->middleware(['throttle:6,1'])
        ->name('sms.verification-create');

    Route::get('/sms/verify', [SmsVerificationController::class, 'passcode'])
        ->name('sms.notice');

    Route::post('/sms/new-code-verify', [SmsVerificationController::class, 'new'])
        ->middleware(['throttle:3,1'])
        ->name('sms.verification-new');

    Route::post('/sms/verify-passcode', [SmsVerificationController::class, 'verify'])
        ->middleware(['throttle:6,1'])
        ->name('sms.verify');

    Route::post('/sms/clear', [SmsVerificationController::class, 'clear'])
        ->middleware(['verified', 'sms.verified'])
        ->name('sms.clear');
});
