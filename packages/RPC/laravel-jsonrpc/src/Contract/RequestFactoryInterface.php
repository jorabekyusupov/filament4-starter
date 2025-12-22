<?php
declare(strict_types=1);

namespace Upgate\LaravelJsonRpc\Contract;

use RPC\laraveluse RPC\laravelinterface RequestFactoryInterface
{

    /**
     * @param string $payloadJson
     * @return ExecutableInterface
     */
    public function createFromPayload(string $payloadJson): ExecutableInterface;

    /**
     * @param \stdClass $requestData
     * @return RequestInterface
     */
    public function createRequest(\stdClass $requestData): RequestInterface;

}