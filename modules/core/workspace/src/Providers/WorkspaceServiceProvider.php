<?php

namespace Modules\Workspace\Providers;

use Modules\App\Providers\BaseServiceProvider;

class WorkspaceServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        $this->name = 'workspace';
        parent::boot();


    }
}
