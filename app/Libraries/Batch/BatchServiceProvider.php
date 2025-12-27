<?php

namespace App\Libraries\Batch;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;

class BatchServiceProvider extends ServiceProvider
{

    public $bindings = [
        BatchInterface::class => Batch::class
    ];
    public function register()
    {
        $this->app->bind('Batch', function ($app) {
            return new Batch($app->make(DatabaseManager::class));
        });
    }
}
