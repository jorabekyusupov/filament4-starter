<?php

namespace Modules\ActivityLog\Providers;


use Illuminate\Support\Facades\Gate;
use Modules\ActivityLog\Models\CustomActivityModel;
use Modules\ActivityLog\Policies\ActivityLogPolicy;
use Modules\App\Providers\BaseServiceProvider;

class ActivityLogServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        Gate::policy(CustomActivityModel::class, ActivityLogPolicy::class);

        $this->name = 'activity-log';
        parent::boot();
	}
}
