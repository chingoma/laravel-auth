<?php

namespace Lockminds\LaravelAuth\Helpers;

use Illuminate\Support\Facades\Mail;
use Lockminds\LaravelAuth\Mail\Auth\Otp\StoreAndSendOTP;
use Lockminds\LaravelAuth\Models\Otp;
use Lockminds\LaravelAuth\Models\User;

class Auths
{
    public static function storeAndSendOTP($key): void
    {
        try {
            $user = User::find($key);
            $otp = rand(100000, 999999);
            $store = new Otp();
            $store->user_id = $user->id;
            $store->otp = $otp;
            $store->expires_at = now()->addMinutes(config('auth.otp.ttl'))->toDateTimeString();
            $store->save();
            $mailable = new StoreAndSendOTP($otp);
            Mail::to($user)->queue($mailable);
        } catch (\Exception $exception) {
            report($exception);
        }
    }
}
