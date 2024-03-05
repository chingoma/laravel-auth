<?php

namespace Lockminds\LaravelAuth\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lockminds\LaravelAuth\Helpers\Responses;

class OTPVerifiedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $id = auth()->id();
        $otp = Cache::get('otp-verified-'.$id);
        if (empty($otp)) {
            return Responses::unauthorized(code: 400);
        }

        return $next($request);
    }
}
