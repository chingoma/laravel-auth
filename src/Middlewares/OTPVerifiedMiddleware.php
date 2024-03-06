<?php

namespace Lockminds\LaravelAuth\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lockminds\LaravelAuth\Helpers\Responses;

class OTPVerifiedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $otpStatus = (new Parser(new JoseEncoder()))->parse($token)->claims()->get('otp');

        if ($otpStatus !== 'valid') {
            return Responses::unauthorized(code: 400);
        }

        return $next($request);
    }
}
