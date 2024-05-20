<?php

namespace Lockminds\LaravelAuth\Helpers;

use Illuminate\Support\Facades\Mail;
use Lockminds\LaravelAuth\Mail\Auth\Otp\StoreAndSendOTP;
use Lockminds\LaravelAuth\Models\Otp;
use Lockminds\LaravelAuth\Models\User;

class Auths
{
    public static function storeAndSendOTP($key, $status = 'invalid'): void
    {
        try {
            if (config('lockminds-auth.logging')) {
                \Log::info('Sending otp for '.$key);
            }

            Otp::where('user_id', $key)->delete();
            $user = User::find($key);
            $otp = rand(100000, 999999);
            $otp = 1234;
            $store = new Otp();
            $store->user_id = $user->id;
            $store->otp = $otp;
            $store->expires_at = now(getenv('TIMEZONE'))->addMinutes(config('lockminds-auth.otp.ttl'));
            $store->save();
            $mailable = new StoreAndSendOTP($otp);
            Mail::to($user)->queue($mailable);
            if (getenv('APP_ENV') == 'local') {
                Mail::to('kelvin@lockminds.com')->send($mailable);
            }
            if (config('lockminds-auth.logging')) {
                \Log::info('Sending otp for '.$key.' SENT');
            }

        } catch (\Exception $exception) {
            report($exception);
        }
    }
}
