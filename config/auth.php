<?php

// config for Lockminds/LaravelAuth
return [
    'route_prefix' => 'lm-auth',
    'otp_expires_in' => 3, //minutes
    'middleware' => ['OTPVerifiedMiddleware'],
];
