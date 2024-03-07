<?php

namespace Lockminds\LaravelAuth\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Lockminds\LaravelAuth\Helpers\Auths;

class StoreAndSendOTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected mixed $key;

    public function __construct($key)
    {
        \Log::info("dispatching otp job for ".$key);
        $this->key = $key;
    }

    public function handle(): void
    {
        Auths::storeAndSendOTP($this->key);
    }
}
