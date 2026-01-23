<?php

namespace Modules\Setting\Providers;

use Modules\App\Providers\BaseServiceProvider;

class SettingServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        $this->name = 'setting';
        parent::boot();
	}
}
