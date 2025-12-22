<?php

namespace Modules\Translate\Providers;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\ServiceProvider;

class TranslateServiceProvider extends ServiceProvider
{

    public function register(): void
	{

	}
	
	public function boot(): void
	{
        $name = 'translate';
        $reflector = new \ReflectionClass($this);
        $dir = dirname($reflector->getFileName());
        $this->mergeConfigFrom($dir . '/../../config/config.php', $name);
        $this->loadRoutesFrom($dir . '/../../routes/route.php');
        $this->loadJsonTranslationsFrom($dir . '/../../lang');
        $this->loadTranslationsFrom($dir . '/../../lang', $name);
        $this->loadViewsFrom($dir . '/../../resources/views', $name);
	}
}
