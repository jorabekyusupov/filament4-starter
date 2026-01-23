<?php

namespace Jora\StarterRpc\DTO\Response;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Sajya\Client\Response;

class BaseResponse
{
    use Conditionable;

    public const ERROR_PARSE = -32700;
    public const ERROR_INVALID_REQUEST = -32600;
    public const ERROR_METHOD_NOT_FOUND = -32601;
    public const ERROR_INVALID_PARAMS = -32602;
    public const ERROR_INTERNAL = -32603;

    protected const ERROR_MESSAGES = [
        self::ERROR_INVALID_PARAMS => 'jsonrpc.errors.invalid_params',
        self::ERROR_INVALID_REQUEST => 'jsonrpc.errors.invalid_request',
        self::ERROR_METHOD_NOT_FOUND => 'jsonrpc.errors.method_not_found',
        self::ERROR_PARSE => 'jsonrpc.errors.parse_error',
        self::ERROR_INTERNAL => 'jsonrpc.errors.internal_error',
    ];

    protected Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function getId(): ?string
    {
        return $this->response->id();
    }

    public function isSuccess(): bool
    {
        return $this->response->error() === null;
    }

    public function failed(): bool
    {
        return ! $this->isSuccess();
    }

    public function hasError(): bool
    {
        return $this->failed();
    }

    public function getResult(?string $key = null, mixed $default = null): mixed
    {
        $result = $this->response->result();

        if ($key === null) {
            return $result;
        }

        return data_get($result, $key, $default);
    }

    public function getStatus(?string $default = null): ?string
    {
        $result = $this->response->result();

        if (! is_array($result)) {
            return $default;
        }

        return (string) data_get($result, 'status', $default);
    }

    public function isStatus(string $status): bool
    {
        return $this->getStatus() === $status;
    }

    public function getData(?string $path = null, mixed $default = null): mixed
    {
        $result = $this->response->result();

        if (! is_array($result)) {
            return $path === null ? $result : $default;
        }

        $payload = Arr::get($result, 'data', $result);

        if ($path === null) {
            return $payload;
        }

        return data_get($payload, $path, $default);
    }

    public function getError(): ?array
    {
        $error = $this->response->error();

        return $error === null ? null : (array) $error;
    }

    public function getErrorCode(): ?int
    {
        return data_get($this->getError(), 'code');
    }

    public function getErrorMessage(): ?string
    {
        $message = data_get($this->getError(), 'message');

        if (filled($message)) {
            return $message;
        }

        return $this->translateErrorCode($this->getErrorCode());
    }

    public function getErrorData(?string $path = null, mixed $default = null): mixed
    {
        $data = data_get($this->getError(), 'data');

        if ($path === null) {
            return $data;
        }

        return data_get($data, $path, $default);
    }

    public function getValidationErrors(): array
    {
        $validation = data_get($this->getError(), 'data.validation', []);

        if (is_array($validation)) {
            return $validation;
        }

        return [];
    }

    public function hasValidationErrors(): bool
    {
        return $this->getErrorCode() === self::ERROR_INVALID_PARAMS
            || ! empty($this->getValidationErrors());
    }

    public function translateErrorCode(?int $code): ?string
    {
        if ($code === null) {
            return null;
        }

        $key = self::ERROR_MESSAGES[$code] ?? null;

        if ($key === null) {
            return null;
        }

        return __($key);
    }
}
