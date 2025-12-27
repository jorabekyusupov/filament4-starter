<?php

namespace Modules\RolePermission\Providers;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\ServiceProvider;

class RolePermissionServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        $this->name = 'role-permission';
        parent::boot();
	}
}
