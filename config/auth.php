<?php

// config for Lockminds/LaravelAuth
return [
    "route_prefix" => "laravel-auth",
    "otp_expires_in" => 3, //minutes
    "middleware" => ["OTPVerifiedMiddleware"],
];
