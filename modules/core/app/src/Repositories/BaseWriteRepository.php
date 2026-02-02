<?php

namespace Modules\App\Repositories;

class BaseWriteRepository implements BaseWriteRepositoryInterface
{
    protected function filterNullAndEmpty(array $data): array
    {
        return array_filter($data, static function ($value, $key) {
            return $value !== null && $value !== "";
        }, ARRAY_FILTER_USE_BOTH);
    }
}