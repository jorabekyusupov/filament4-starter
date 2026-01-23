<?php

namespace Modules\Application\Providers;

use Modules\App\Providers\BaseServiceProvider;

class ApplicationServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
		parent::register();
	}

	public function boot(): void
	{
		$this->name = 'application';
		parent::boot();
	}
}
