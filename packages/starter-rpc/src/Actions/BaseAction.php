<?php

namespace Jora\StarterRpc\Actions;

use Jora\StarterRpc\Clients\SecureRpcClient;
use Jora\StarterRpc\DTO\Response\BaseResponse;

abstract class BaseAction
{
    protected SecureRpcClient $client;

    public function __construct(SecureRpcClient $client)
    {
        $this->client = $client;
    }
    abstract public function handle($payload): BaseResponse;
}