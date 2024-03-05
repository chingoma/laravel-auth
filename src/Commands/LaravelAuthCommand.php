<?php

namespace Lockminds\LaravelAuth\Commands;

use Illuminate\Console\Command;

class LaravelAuthCommand extends Command
{
    public $signature = 'laravel-auth';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
