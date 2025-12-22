<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\ServiceProvider;

class StubClassNamePrefixServiceProvider extends BaseServiceProvider
{
	public function register(): void
	{
        parent::register();
	}
	
	public function boot(): void
	{
        $this->name = 'StubModuleName';
        parent::boot();
	}
}
