<?php

namespace Modules\MakerModule\Providers;

use Jora\Modular\Console\Commands\Make\MakeModule;
use Modules\App\Providers\BaseServiceProvider;
use Modules\MakerModule\Commands\AppFreshCommand;
use Modules\MakerModule\Commands\SyncModuleCommand;


class MakerModuleServiceProvider extends BaseServiceProvider
{
    protected static bool $alreadyHooked = false;

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        $this->name = 'maker-module';
        parent::boot();

        if (static::$alreadyHooked) {
            return;
        }
        static::$alreadyHooked = true;
//        Event::listen(CommandStarting::class, static function (CommandStarting $event) {
//            if ($event->command === 'db:seed' && !($event->input->getOption('class') !== 'Database\Seeders\DatabaseSeeder' && $event->input->getOption('class') !== null)) {
//                app()->call(MakerModuleSeeder::class);
//            }
//        });
        $this->commands([
            MakeModule::class,
            AppFreshCommand::class,
            SyncModuleCommand::class
        ]);
    }
}
