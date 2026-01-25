<?php

namespace Jora\StarterRpc;

use Jora\StarterRpc\Clients\SecureRpcClient;
use Jora\StarterRpc\Enums\RpcMethod;

class ActionHandler
{
    protected SecureRpcClient $client;
    /**
     * Actionlar ro'yxati (Mapping)
     * Key sifatida Enumning qiymati (value) ishlatiladi
     */
    protected array $actions = [];

    public function __construct()
    {
        $this->client = new SecureRpcClient(
            config('starter-rpc.rpc_endpoint'),
            config('starter-rpc.rpc_username'),
            config('starter-rpc.rpc_password'),
            config('starter-rpc.rpc_secret_key')
        );

        $this->actions = [
            //RpcMethod::GET_CANDIDATE_LIST->value => \Jora\HrCandidateRpcClient\Actions\GetCandidateListAction::class,
            // ...
        ];

    }

    /**
     * Actionni chaqirish
     * * @param RpcMethod $method  <--- Endi bu yerda String emas, Enum keladi
     * @param mixed $payload
     */
    public function call(RpcMethod $method, $payload)
    {
        // Enumdan string qiymatini olamiz ('person.create')
        $methodName = $method->value;

        if (!array_key_exists($methodName, $this->actions)) {
            throw new Exception("Action topilmadi: {$method->name}");
        }

        $actionClass = $this->actions[$methodName];
        return new $actionClass($this->client)->handle($payload);
    }

}