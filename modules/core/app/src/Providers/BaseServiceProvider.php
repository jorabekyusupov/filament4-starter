<?php

namespace Modules\App\Providers;

use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    public array $bindings = [];

    public string $name = '';

    public function register(): void
    {

    }

    public function boot(): void
    {
        $reflector = new \ReflectionClass($this);
        $dir = dirname($reflector->getFileName());
        $this->loadMigrationsFrom($dir . '/../../migrations');
        $this->mergeConfigFrom($dir . '/../../config/config.php', $this->name);
        $this->loadRoutesFrom($dir . '/../../routes/route.php');
        $this->loadJsonTranslationsFrom($dir . '/../../lang');
        $this->loadTranslationsFrom($dir . '/../../lang', $this->name);
        $this->loadViewsFrom($dir . '/../../resources/views', $this->name);

    }


}