<?php

namespace Jora\StarterRpc\Clients;

use Jora\StarterRpc\Services\SignatureService;
use Sajya\Client\Client;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Sajya\Client\Response;
// Kerakli klasslarni import qilamiz


class SecureRpcClient
{
    protected Client $client;

    public function __construct(string $endpoint, string $username, string $password, ?string $secretKey = null)
    {
        // 1. Kerakli servislarni tayyorlaymiz
        $signatureService = new SignatureService();

        // Handler klassdan obyekt olamiz
        $signatureHandler = new HmacSignatureHandler($secretKey, $signatureService);

        // 2. Guzzle Handler Stack yaratamiz
        $stack = HandlerStack::create();

        // 3. Middleware qo'shamiz
        // mapRequest bizning handlerimizni chaqiradi
        $stack->push(Middleware::mapRequest($signatureHandler));

        // 4. Sajya Clientni initsializatsiya qilamiz
        $pendingRequest = Http::baseUrl($endpoint)
            ->withHeaders(['Accept-Language' => app()->getLocale()])
            ->withBasicAuth($username, $password)
            ->withOptions(['handler' => $stack]);

        $this->client = new Client($pendingRequest);
    }

    public function execute(string $method, array $params = []): Response
    {
        $params['_method'] = $method;
        return $this->client->execute($method, $params);
    }

    public function batch(callable $callback)
    {
        return $this->client->batch($callback);
    }

    public function notify(string $method, array $params = [])
    {
        $this->client->notify($method, $params);
    }
}