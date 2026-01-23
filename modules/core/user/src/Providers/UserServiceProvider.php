<?php

namespace Modules\User\Providers;

use Modules\App\Providers\BaseServiceProvider;

class UserServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        parent::boot();
        $this->name = 'user';
	}
}
