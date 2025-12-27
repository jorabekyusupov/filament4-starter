<?php

namespace Modules\MakerModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AppFreshCommand extends Command
{
    protected $signature = 'app:fresh';

    protected $description = 'Command description';

    public function handle(): void
    {
        Artisan::call('migrate:fresh', [
            '--force' => true,
        ]);
        $this->info('Database migrated fresh.');
        Artisan::call('db:seed');
        $this->info('Database seeded.');
        Artisan::call('optimize:clear');
        $this->info('Application optimized.');
        Artisan::call('optimize');
        $this->info('Application cache optimized.');
        $this->info('App fresh command completed successfully.');

    }
}
