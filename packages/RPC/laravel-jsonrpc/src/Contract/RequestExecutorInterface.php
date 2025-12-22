<?php
declare(strict_types=1);

namespace Upgate\LaravelJsonRpc\Contract;

use jsonrpc\src\Server\RequestResponse;
use RPC\laraveluse RPC\laravel

interface RequestExecutorInterface
{

    /**
     * @param RequestInterface $request
     * @return jsonrpc\src\Server\RequestResponse
     */
    public function execute(RequestInterface $request);

}