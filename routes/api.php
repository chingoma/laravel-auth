<?php

use Illuminate\Support\Facades\Route;
use Lockminds\LaravelAuth\Controllers\AccessController;
use Lockminds\LaravelAuth\Controllers\AuthorizedAccessController;
use Lockminds\LaravelAuth\Controllers\UsersController;
use Lockminds\LaravelAuth\Middlewares\OTPVerifiedMiddleware;

Route::post('get-token', [AccessController::class, 'issueToken']);

Route::post('get-client', [AccessController::class, 'createClient']);

Route::post('send-reset-password-link', [AccessController::class, 'sendResetPasswordLink']);

Route::post('change-password', [AccessController::class, 'changePassword']);

Route::post('generate-user', [AccessController::class, 'generateUser']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [AuthorizedAccessController::class, 'destroy']);
    Route::post('refresh-token', [AccessController::class, 'issueToken']);
    Route::post('reset-password', [AccessController::class, 'resetPassword']);
    Route::post('resend-otp', [AccessController::class, 'resendOTP']);
    Route::post('verify-otp', [AccessController::class, 'verifyOTP']);

    Route::group(['middleware' => [OTPVerifiedMiddleware::class]], function () {
        Route::get('profile', [UsersController::class, 'profile']);
        Route::get('users', [UsersController::class, 'users']);
    });

});
