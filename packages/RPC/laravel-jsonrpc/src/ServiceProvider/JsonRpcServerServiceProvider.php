<?php
declare(strict_types=1);

namespace Upgate\LaravelJsonRpc\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use jsonrpc\src\Contract\ServerInterface as JsonRpcServerContract;
use jsonrpc\src\Server\ServerFactory;

class JsonRpcServerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(
            ServerFactory::class,
            function () {
                return new ServerFactory($this->app);
            }
        );

        $this->app->singleton(
            JsonRpcServerContract::class,
            function () {
                return $this->app->make(ServerFactory::class)->make();
            }
        );
    }

}
