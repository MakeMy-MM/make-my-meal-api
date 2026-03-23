<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test:command';

    protected $description = 'Test command used to try out new features and debug issues';

    public function handle(): int
    {
        $this->info('Test command started successfully.');

        return self::SUCCESS;
    }
}
