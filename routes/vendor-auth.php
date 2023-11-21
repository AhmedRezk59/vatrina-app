<?php

use App\Http\Controllers\Vendor\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Vendor\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Vendor\Auth\NewPasswordController;
use App\Http\Controllers\Vendor\Auth\PasswordResetLinkController;
use App\Http\Controllers\Vendor\Auth\RegisteredUserController;
use App\Http\Controllers\Vendor\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;


Route::prefix('vendor')->name('vendor.')->group(function () {

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.store');

    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class,'__invoke'])
        ->middleware(['auth', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:api-vendor')
        ->name('logout');
});
