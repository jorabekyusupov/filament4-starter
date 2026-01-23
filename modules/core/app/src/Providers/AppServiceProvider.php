<?php

namespace Modules\App\Providers;

class AppServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        $this->name = 'app';
        parent::boot();
	}
}
