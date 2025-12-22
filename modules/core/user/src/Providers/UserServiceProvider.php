<?php

namespace Modules\User\Providers;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\ServiceProvider;

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
