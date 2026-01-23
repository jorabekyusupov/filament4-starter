<?php

namespace StubModuleNamespace\StubClassNamePrefix\Providers;

use Modules\App\Providers\BaseServiceProvider;

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
