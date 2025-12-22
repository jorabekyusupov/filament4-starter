<?php

namespace Modules\Language\Commands;

use Illuminate\Console\Command;
use Modules\Language\Seeders\DefaultLangSeeder;

class InsertDefaultLangCommand extends Command
{
    protected $signature = 'insert-default-lang';

    protected $description = 'Command description';

    public function handle(): void
    {
        app(DefaultLangSeeder::class)->run();
    }
}
