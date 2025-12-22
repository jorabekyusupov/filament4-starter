<?php
declare(strict_types=1);

namespace Upgate\LaravelJsonRpc\Contract;

use jsonrpc\src\Server\RequestParams;

interface RouteDispatcherInterface
{

    /**
     * @param RouteInterface $route
     * @param jsonrpc\src\Server\RequestParams $requestParams
     * @return mixed
     */
    public function dispatch(RouteInterface $route, ?RequestParams $requestParams = null);

    /**
     * @param string|null $controllerNamespace
     * @return $this
     */
    public function setControllerNamespace(?string $controllerNamespace = null): RouteDispatcherInterface;

}
