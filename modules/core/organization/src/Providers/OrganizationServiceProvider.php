<?php

namespace Modules\Organization\Providers;

use App\Providers\BaseServiceProvider;
use Illuminate\Support\ServiceProvider;

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
