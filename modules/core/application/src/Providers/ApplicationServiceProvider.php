<?php

namespace Modules\Application\Providers;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\ServiceProvider;

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
