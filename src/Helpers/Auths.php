<?php

namespace Lockminds\LaravelAuth\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Lockminds\LaravelAuth\Mail\Auth\Otp\StoreAndSendOTP;
use Lockminds\LaravelAuth\Models\User;

class Auths
{
    public static function storeAndSendOTP($key): void
    {
        try {
            $user = User::find($key);
            $otp = rand(100000, 999999);
            $key = auth()->id();
            Cache::delete($key);
            Cache::put([$key => $otp], now()->addMinutes(config('auth.otp_expires_in')));
            $mailable = new StoreAndSendOTP($otp);
            Mail::to($user)->queue($mailable);
        } catch (\Exception $exception) {
            report($exception);
        }
    }
}
