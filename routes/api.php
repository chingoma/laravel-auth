<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;
use Lockminds\LaravelAuth\Controllers\AccessController;
use Lockminds\LaravelAuth\Controllers\ProfileController;
use Lockminds\LaravelAuth\Middlewares\OTPVerifiedMiddleware;

Route::post('get-token', [AccessController::class, 'issueToken']);

Route::post('get-client', [AccessController::class, 'createClient']);

Route::post('send-reset-password-link', [AccessController::class, 'sendResetPasswordLink']);

Route::post('change-password', [AccessController::class, 'changePassword']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [AuthorizedAccessTokenController::class, 'destroy']);
    Route::post('refresh-token', [AccessController::class, 'issueToken']);
    Route::post('reset-password', [AccessController::class, 'resetPassword']);
    Route::post('resend-otp', [AccessController::class, 'resendOTP']);

    Route::group(['middleware' => [OTPVerifiedMiddleware::class]], function () {
        Route::get('profile', [ProfileController::class, 'profile']);
    });

});
