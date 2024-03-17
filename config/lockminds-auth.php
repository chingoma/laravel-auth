<?php

// config for Lockminds/LaravelAuth
return [
    'route_prefix' => 'lm-auth',
    'otp' => [
        'ttl' => 3,
    ], //minutes
    'middleware' => ['OTPVerifiedMiddleware'],
    'logging' => true,
];
