<?php

namespace Lockminds\LaravelAuth\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lockminds\LaravelAuth\LaravelAuth
 */
class LaravelAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Lockminds\LaravelAuth\LaravelAuth::class;
    }
}
