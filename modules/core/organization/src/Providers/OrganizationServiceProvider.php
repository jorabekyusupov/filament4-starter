<?php

namespace Modules\Organization\Providers;

use Modules\App\Providers\BaseServiceProvider;

class OrganizationServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        $this->name = 'organization';
        parent::boot();


    }
}
