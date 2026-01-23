<?php

namespace Jora\StarterRpc\Providers;

use Illuminate\Support\ServiceProvider;
use Jora\StarterRpc\ActionHandler;


class JRPCServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ActionHandler::class, function ($app) {
            return new ActionHandler();
        });
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'starter-rpc');
    }
}
