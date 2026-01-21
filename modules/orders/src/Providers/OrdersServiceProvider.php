<?php

namespace Modules\Orders\Providers;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\ServiceProvider;

class OrdersServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        $this->name = 'orders';
        parent::boot();
	}
}
