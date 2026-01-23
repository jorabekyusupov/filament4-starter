<?php

namespace Modules\RolePermission\Providers;

use Modules\App\Providers\BaseServiceProvider;

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
